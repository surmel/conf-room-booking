test:
	./vendor/bin/pest

stan:
	./vendor/bin/phpstan analyse

check: stan test
	@echo "All checks passed ✓"
