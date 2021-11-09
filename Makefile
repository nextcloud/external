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

dev-setup: clean-dev npm-init

release: appstore create-tag

build-js:
	npm run dev

build-js-production:
	npm run build

watch-js:
	npm run watch

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

appstore: clean npm-init build-js-production
	mkdir -p $(sign_dir)
	rsync -a \
	--exclude=/build \
	--exclude=/docs \
	--exclude=/translationfiles \
	--exclude=/.tx \
	--exclude=/tests \
	--exclude=/.git \
	--exclude=/screenshots \
	--exclude=/.github \
	--exclude=/l10n/l10n.pl \
	--exclude=/CONTRIBUTING.md \
	--exclude=/issue_template.md \
	--exclude=/node_modules \
	--exclude=/src \
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
	--exclude=/webpack.config.js \
	$(project_dir)/ $(sign_dir)/$(app_name)
	tar -czf $(build_dir)/$(app_name).tar.gz \
		-C $(sign_dir) $(app_name)
	@if [ -f $(cert_dir)/$(app_name).key ]; then \
		echo "Signing package…"; \
		openssl dgst -sha512 -sign $(cert_dir)/$(app_name).key $(build_dir)/$(app_name).tar.gz | openssl base64; \
	fi
