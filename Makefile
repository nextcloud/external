app_name=external

project_dir=$(CURDIR)/../$(app_name)
build_dir=$(CURDIR)/build/artifacts
appstore_dir=$(build_dir)/appstore
source_dir=$(build_dir)/source
sign_dir=$(build_dir)/sign
package_name=$(app_name)
cert_dir=$(HOME)/.nextcloud/certificates
version+=master

all: appstore build-js-production

dev-setup: clean-dev composer-install-dev npm-init

release: appstore create-tag

build-js:
	npm run dev

build-js-production:
	npm run build

watch-js:
	npm run watch

composer-install-dev:
	composer install

composer-install-production:
	composer install --no-dev

test:
	npm run test:unit

lint:
	npm run lint

lint-fix:
	npm run lint:fix

npm-init:
	npm ci

npm-update:
	npm update

clean:
	rm -rf js/dist/*
	rm -rf $(build_dir)

clean-dev: clean
	rm -rf node_modules

create-tag:
	git tag -s -a v$(version) -m "Tagging the $(version) release."
	git push origin v$(version)

js-templates:
	handlebars -n OCA.External.Templates js/templates -f js/templates.js
	rm -rf node_modules

appstore: clean composer-install-production npm-init build-js-production
	mkdir -p $(sign_dir)
	rsync -a \
	--exclude=/.git \
	--exclude=/.github \
	--exclude=/.tx \
	--exclude=/build \
	--exclude=/docs \
	--exclude=/l10n/l10n.pl \
	--exclude=/node_modules \
	--exclude=/screenshots \
	--exclude=/src \
	--exclude=/tests \
	--exclude=/translationfiles \
	--exclude=/vendor \
	--exclude=/vendor-bin \
	--exclude=.php-cs-fixer.cache \
	--exclude=.php-cs-fixer.dist.php \
	--exclude=/composer.json \
	--exclude=/composer.lock \
	--exclude=/README.md \
	--exclude=/.gitattributes \
	--exclude=/.gitignore \
	--exclude=/.scrutinizer.yml \
	--exclude=/.travis.yml \
	--exclude=/.drone.yml \
	--exclude=/babel.config.js \
	--exclude=/.eslintrc.js \
	--exclude=/Makefile \
	--exclude=/package.json \
	--exclude=/package-lock.json \
	--exclude=/psalm.xml \
	--exclude=/webpack.config.js \
	$(project_dir)/ $(sign_dir)/$(app_name)
	tar -czf $(build_dir)/$(app_name).tar.gz \
		-C $(sign_dir) $(app_name)
	@if [ -f $(cert_dir)/$(app_name).key ]; then \
		echo "Signing package…"; \
		openssl dgst -sha512 -sign $(cert_dir)/$(app_name).key $(build_dir)/$(app_name).tar.gz | openssl base64; \
	fi
