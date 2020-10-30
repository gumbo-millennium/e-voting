@setup
// Check required variables
$required = [
'remote' => 'clone URL',
'branch' => 'branch'
];

// Check required vars
foreach ($required as $var => $label) {
if (empty($$var)) {
throw new Exception("The $label has not been set. Set it using --$var=[value]");
}
}

// Get branch name
$branchBits = explode('/', $branch);
$branch = array_pop($branchBits);

// Get hash if missing
$hash ??= trim(`git log -1 --format='%H'`);

// Set env from branch
$isProduction = $branch === 'production';
$env = $isProduction ? 'production' : 'staging';
$domain = $isProduction ? 'e-voting.gumbo-millennium.nl' : 'beta.e-voting.gumbo-millennium.nl';

// Settings
$logFormat = '%h %s (%cr, %cn)'; // see `man git log`

// Deploy name
$deployName = (new DateTime('now', new DateTimeZone('UTC')))->format('Y-m-d--H-i-s');

// Paths
$domainRoot = "\$HOME/domains/{$domain}";
$root = "{$domainRoot}/laravel";

// Get public dir
$publicDir = "{$domainRoot}/public_html";

// Get specific paths
$deployPath = "$root/deployments/{$deployName}";
$livePath = "$root/live";
$envPath = "$root/environment/config.env";
$storagePath = "$root/storage";
$backupOldPath = "$root/deployments/backup-{$deployName}";
$branchSlug = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($branch)), '-');

// Paths that must exist
$paths = [
$root,
dirname($livePath),
dirname($envPath),
dirname($backupOldPath),
];
@endsetup

@servers(['web' => 'deploy.local'])

@story('deploy')
deployment_init
deployment_precheck
deployment_clone
deployment_describe
deployment_link
deployment_install
deployment_build
deployment_down
deployment_migrate
deployment_cache
deployment_up
deployment_cleanup
health_check
@endstory

@task('deployment_init')
{{-- Pre-deploy validation --}}
echo -e "\nEnsuring working directories exist"
@foreach ($paths as $path)
test -d "{{ $path }}" || mkdir -p "{{ $path }}"
@endforeach

{{-- Make deployment directory --}}
echo -e "\nCreating clone path"
mkdir -p "{{ $deployPath }}"

if [ -L "{{ $livePath }}" ]; then
{{-- Report status --}}
echo -e "\nLive path is currently linked to $( basename "$( realpath "{{ $livePath }}/" )" )"
elif [ -d "{{ $livePath }}" ]; then
{{-- Move live directory if it's a normal directory--}}
echo -e "\nMoving live path to $( basename "{{ $backupOldPath }}" )"
mv "{{ $livePath }}" "{{ $backupOldPath }}"
ln -s "{{ $backupOldPath }}" "{{ $livePath }}"
elif [ ! -L "{{ $livePath }}" ]; then
{{-- Ensure a directory exists --}}
echo -e "\nMaking new current and link it to this deploy"
ln -s "{{ $deployPath }}" "{{ $livePath }}"

echo -e "\nAlso linking public path"
rm -rvf "{{ $publicDir }}"
ln -s "{{ $livePath }}/public" "{{ $publicDir }}"
fi
@endtask

@task('deployment_precheck')
    LIVE_ARTISAN="{{ $livepath }}/artisan"
    if [ -f "$LIVE_ARTISAN" ];
        if ! php "$LIVE_ARTISAN" vote:can-deploy; then
            echo "Deployment blocked by application"
            exit 1
        fi
    fi
@endtask

@task('deployment_clone')
{{-- Enter deploy repo --}}
cd "{{ $deployPath }}"

{{-- Clone repo, but don't checkout yet --}}
echo -e "\nCloning {{ $remote }} and checking out {{ $branch }}."
git clone \
--no-checkout \
"{{ $remote }}" \
"{{ $deployPath }}"

{{-- Check out as branch --}}
echo -e "\nChecking out {{ $hash }} as 'deployment/{{ $branchSlug }}-{{ $deployName }}'"
git checkout -b "deployment/{{ $branchSlug }}-{{ $deployName }}" "{{ $hash }}"

{{-- Init submodules --}}
echo -e "\nFetching submodules"
git submodule update --init --force
@endtask

@task('deployment_describe')
cd "{{ $deployPath }}"
{{-- Get latest hash of current and active --}}
NEW_HASH=$( cd "{{ $deployPath }}" && git log -1 --format='%H' )
OLD_HASH=$( cd "{{ $livePath }}" && git log -1 --format='%H' )

{{-- Also get log of old version --}}
NEW_VERSION=$( cd "{{ $deployPath }}" && git log -1 --format="{{ $logFormat }}" )
OLD_VERSION=$( cd "{{ $livePath }}" && git log -1 --format="{{ $logFormat }}" )

{{-- Show diff --}}
echo -e "\n"
echo "Currently live: ${OLD_VERSION}"
echo "Currently deploying: ${NEW_VERSION}"
echo -e "\nChanges since last version:\n"
git log --decorate --graph --format="{{ $logFormat }}" "${OLD_HASH}..${NEW_HASH}" 2>dev/null || true
@endtask

@task('deployment_link')
{{-- Ensure data is available --}}
if [ ! -d "{{ $storagePath }}" ]; then
echo -e "\nCreating new storage directory"
cp -vr "{{ $deployPath }}/storage" "{{ $storagePath }}"
fi

{{-- Ensure env is available --}}
if [ ! -f "{{ $envPath }}" ]; then
echo -e "\nCreating new environment config"
ENV_DIR="$( dirname "{{ $envPath }}" )"
test -d "$ENV_DIR" || mkdir -p "$ENV_DIR"
cp -v "{{ $deployPath }}/.env.example" "{{ $envPath }}"
chmod 0600 "{{ $envPath }}"
fi

{{-- Make directories and files --}}
echo -e "\nRemove existing storage"
rm -r "{{ $deployPath }}/storage"

echo -e "\nLink storage"
ln -s "{{ $storagePath }}" "{{ $deployPath }}/storage"

echo -e "\nLink environment config"
ln -s "{{ $envPath }}" "{{ $deployPath }}/.env"
@endtask

@task('deployment_install')
cd "{{ $deployPath }}"

echo -e "\nInstalling Yarn dependencies"
yarn \
--cache-folder="{{ $root }}/cache/node" \
--frozen-lockfile \
--link-duplicates \
--link-folder "{{ $root }}/cache/node-duplicates" \
--prefer-offline \
install

echo -e "\nInstalling Composer dependencies"
composer \
--classmap-authoritative \
--no-dev \
--no-interaction \
--no-progress \
--no-suggest \
install

{{-- Link public storage --}}
echo -e "\nLink public directory to storage"
php "{{ $deployPath }}/artisan" storage:link

{{-- Generate key if missing --}}
source "{{ $deployPath }}/.env"
if [ -z "$APP_KEY" ]; then
php "{{ $deployPath }}/artisan" key:generate
fi
@endtask

@task('deployment_build')
cd "{{ $deployPath }}"

echo -e "\nBuilding front-end"
yarn build

echo -e "\nRemoving node_modules"
rm -rf "{{ $deployPath }}/node_modules"
@endtask

@task('deployment_down')
cd "{{ $deployPath }}"

{{-- Pull down new and current app --}}
echo -e "\nPulling down platform"
php artisan down --retry=5 || true
php "{{ $livePath }}/artisan" down --retry=5 || true

echo -e "\nClearing optimizations"
php "{{ $livePath }}/artisan" optimize:clear
@endtask

@task('deployment_migrate')
cd "{{ $deployPath }}"

{{-- Migrate database --}}
echo -e "\nMigrating database"
php artisan migrate --force
php artisan db:seed --force || true
@endtask

@task('deployment_cache')
cd "{{ $deployPath }}"

{{-- Optimize application --}}
echo -e "\nOptimizing application"
php artisan optimize
php artisan event:cache
@endtask

@task('deployment_up')
cd "{{ $deployPath }}"

{{-- Make backlink to current version --}}
OLD_PATH="$( realpath "{{ $livePath }}/" )"
ln -s "${OLD_PATH}" "{{ $deployPath }}/_previous"

{{-- Switch active version --}}
echo "Switching from $( basename "${OLD_PATH}" ) to $( basename "{{ $deployPath }}" )"
rm "{{ $livePath }}"
ln -s "{{ $deployPath }}" "{{ $livePath }}"

{{-- Start up the server again --}}
echo -e "\nGoing live"
php artisan up

{{-- Get URL --}}
source .env
echo -e "\nApplication is live at ${APP_URL}."
echo ">>URL = ${APP_URL}"
@endtask

@task('deployment_cleanup')
find "$( dirname "{{ $deployPath }}" )" -maxdepth 1 -name "20*" | sort | head -n -4 | xargs rm -Rf
echo "Cleaned up old deployments"
@endtask

@story('rollback')
deployment_rollback
health_check
@endstory

@task('deployment_rollback')
cd "{{ $root }}"
if [ ! -L "{{ $livePath }}/_previous" ]; then
echo "Rollback not supported for this release"
exit 1
fi

if [ ! -e "{{ $livePath }}/_previous/artisan" ]; then
echo "Previous release has been pruned"
exit 1
fi

OLD_VERSION="$( realpath "{{ $livePath }}/_previous" )"
if [ "$( realpath "${OLD_PATH}" )" = "$( realpath "${{ $livePath }}" )" ]; then
echo "Already at latest version"
exit 1
fi

echo -e "\nGoing dark"
php artisan down --retry=5

echo -e "\nRolling back to $( basename "${OLD_VERSION}" )"
rm "{{ $livePath }}"
ln -s "${OLD_VERSION}" "{{ $livePath }}"

echo "Re-running caching"
php artisan optimize:clear
php artisan optimize
php artisan event:cache

echo -e "\nGoing back online"
php artisan up

echo -e "\nRolled back to $( basename "${OLD_VERSION}" )"
@endtask

@task('health_check')
source "{{ $livePath }}/.env"
echo -e "\nRunning health check..."
curl --location --fail "${APP_URL}" > /dev/null
@endtask
