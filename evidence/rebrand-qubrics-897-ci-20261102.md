# PHP validation CI prerequisite

Goal: Native validation CI for qubrics/qubrics-php-mailbox, separate from its A15 rebrand PR under qubrics/qubrics#897.

Status: Coded, not merged. Draft prerequisite only; no publication or deployment.

## Change and scope

- Added PR and main-push validation in `.github/workflows/ci.yml`.
- Read-only contents permission; checkout credentials are not persisted.
- PHP 8.2 matches the minimum runtime declared in `composer.json:7`.
- Checks Composer package metadata strictly and parses every source PHP file.
- No package source, dependency, version, release or publishing configuration changed.
- No new tests, harnesses, ignored failures or validation exceptions.

## Canonical sources

- [Composer validate](https://getcomposer.org/doc/03-cli.md#validate): checks the manifest and returns non-zero for warnings with `--strict`.
- [PHP CLI options](https://www.php.net/manual/en/features.commandline.options.php): `-l` parses without executing source and fails on syntax errors. One file per invocation supports PHP 8.2.
- [Setup PHP 2.36.0](https://github.com/shivammathur/setup-php/tree/2.36.0): pinned to `db91e1a0e48e84637d325a7ee4d2677e146ceec4`, verified with `git ls-remote`.
- Checkout pin follows the existing SDK CI workflow, `actions/checkout@de0fac2e4500dabe0009e67214ff5f5447ce83dd`.

## Verification

Native check inputs: source and `composer.json` from verified `origin/main` `d1f0645c4e8ead48614038328757ee35b7fba10b`; this change does not alter either input. No lockfile exists.

Local environment: PHP 8.4.10, Composer 2.8.9.

| Command | Result |
| --- | --- |
| `composer validate --strict` | Exit 0; `./composer.json is valid` |
| `find src -type f -name '*.php' -print0 \| xargs -0 -n 1 php -l` | Exit 0; 142 PHP files |

Raw local receipts: MAIN `.claude/artifacts/rebrand-qubrics-897-ci/composer-validate.log` and `php-lint.log`.
PHP 8.2 verification is the draft PR's `validate` check. Its check run supplies the CI log and exact commit identity.

Tests: No native runtime test suite exists. Syntax and manifest validation are the authorised prerequisite scope.
Journeys: Not applicable; CI-only change.
Torn down: No process, container or runtime dependency cache created. Worktree retained for draft review.
Parked: Runtime/dependency integration verification remains outside this minimal CI prerequisite.
