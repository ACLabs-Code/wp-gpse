#!/usr/bin/env bash
# Install WordPress and the WordPress test library.
# Usage: install-wp-tests.sh <db-name> <db-user> <db-pass> [db-host] [wp-version]

DB_NAME=${1:-wordpress_test}
DB_USER=${2:-root}
DB_PASS=${3:-}
DB_HOST=${4:-localhost}
WP_VERSION=${5:-latest}

TMPDIR=${TMPDIR-/tmp}
TMPDIR=$(echo "$TMPDIR" | sed -e 's/\/$//')
WP_TESTS_DIR=${WP_TESTS_DIR-$TMPDIR/wordpress-tests-lib}
WP_CORE_DIR=${WP_CORE_DIR-$TMPDIR/wordpress}

download() {
	if command -v curl &>/dev/null; then
		curl -s "$1" >"$2"
	elif command -v wget &>/dev/null; then
		wget -nv -O "$2" "$1"
	fi
}

resolve_wp_tests_tag() {
	if [[ $WP_VERSION =~ ^[0-9]+\.[0-9]+\-(beta|RC)[0-9]+$ ]]; then
		WP_TESTS_TAG="branches/${WP_VERSION%-*}"
	elif [[ $WP_VERSION =~ ^[0-9]+\.[0-9]+$ ]]; then
		WP_TESTS_TAG="branches/$WP_VERSION"
	elif [[ $WP_VERSION =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
		if [[ $WP_VERSION =~ ^[0-9]+\.[0-9]+\.0$ ]]; then
			WP_TESTS_TAG="branches/${WP_VERSION%.*}"
		else
			WP_TESTS_TAG="tags/$WP_VERSION"
		fi
	elif [[ $WP_VERSION == 'nightly' || $WP_VERSION == 'trunk' ]]; then
		WP_TESTS_TAG="trunk"
	else
		download https://api.wordpress.org/core/version-check/1.7/ /tmp/wp-latest.json
		LATEST_VERSION=$(grep -o '"version":"[^"]*' /tmp/wp-latest.json | head -1 | sed 's/"version":"//')
		if [[ -z "$LATEST_VERSION" ]]; then
			echo "Could not determine latest WordPress version."
			exit 1
		fi
		WP_TESTS_TAG="tags/$LATEST_VERSION"
	fi
}

install_wp() {
	if [ -d "$WP_CORE_DIR" ]; then return; fi
	mkdir -p "$WP_CORE_DIR"

	if [[ $WP_VERSION == 'nightly' || $WP_VERSION == 'trunk' ]]; then
		mkdir -p "$TMPDIR/wordpress-trunk"
		svn export --quiet https://core.svn.wordpress.org/trunk "$TMPDIR/wordpress-trunk/wordpress"
		mv "$TMPDIR/wordpress-trunk/wordpress" "$WP_CORE_DIR"
	else
		local ARCHIVE=${WP_VERSION/latest/latest}
		[[ $WP_VERSION != 'latest' ]] && ARCHIVE="wordpress-${WP_VERSION}"
		download "https://wordpress.org/${ARCHIVE}.tar.gz" "$TMPDIR/wordpress.tar.gz"
		tar --strip-components=1 -zxmf "$TMPDIR/wordpress.tar.gz" -C "$WP_CORE_DIR"
	fi
}

install_test_suite() {
	if [[ $(uname -s) == 'Darwin' ]]; then
		local ioption='-i .bak'
	else
		local ioption='-i'
	fi

	if [ ! -d "$WP_TESTS_DIR/includes" ]; then
		mkdir -p "$WP_TESTS_DIR"
		svn co --quiet "https://develop.svn.wordpress.org/${WP_TESTS_TAG}/tests/phpunit/includes/" "$WP_TESTS_DIR/includes"
		svn co --quiet "https://develop.svn.wordpress.org/${WP_TESTS_TAG}/tests/phpunit/data/" "$WP_TESTS_DIR/data"
	fi

	if [ ! -f "$WP_TESTS_DIR/wp-tests-config.php" ]; then
		download "https://develop.svn.wordpress.org/${WP_TESTS_TAG}/wp-tests-config-sample.php" "$WP_TESTS_DIR/wp-tests-config.php"
		WP_CORE_DIR_TRIMMED=$(echo "$WP_CORE_DIR" | sed 's:/*$::')
		sed $ioption "s:dirname( __FILE__ ) . '/src/':'${WP_CORE_DIR_TRIMMED}/':" "$WP_TESTS_DIR/wp-tests-config.php"
		sed $ioption "s/youremptytestdbnamehere/$DB_NAME/" "$WP_TESTS_DIR/wp-tests-config.php"
		sed $ioption "s/yourusernamehere/$DB_USER/" "$WP_TESTS_DIR/wp-tests-config.php"
		sed $ioption "s/yourpasswordhere/$DB_PASS/" "$WP_TESTS_DIR/wp-tests-config.php"
		sed $ioption "s|localhost|${DB_HOST}|" "$WP_TESTS_DIR/wp-tests-config.php"
	fi
}

install_db() {
	local PARTS
	IFS=':' read -ra PARTS <<< "$DB_HOST"
	local DB_HOSTNAME=${PARTS[0]}
	local DB_PORT=${PARTS[1]}
	local EXTRA=""

	if [ -n "$DB_PORT" ]; then
		EXTRA=" --host=$DB_HOSTNAME --port=$DB_PORT --protocol=tcp --skip-ssl"
	elif [ -n "$DB_HOSTNAME" ]; then
		EXTRA=" --host=$DB_HOSTNAME --protocol=tcp --skip-ssl"
	fi

	mysqladmin create "$DB_NAME" --user="$DB_USER" --password="$DB_PASS" $EXTRA 2>/dev/null || true
}

set -ex
resolve_wp_tests_tag
install_wp
install_test_suite
install_db
