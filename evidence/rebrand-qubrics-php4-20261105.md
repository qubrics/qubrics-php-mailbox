# PHP Mailbox rebrand candidate

Goal: Prepare `qubrics/mailbox` 3.0.0 with the `Qubrics\Mailbox\` namespace and Qubrics default host for review.

Status: Coded and verified locally. No commit, push, PR, tag, Packagist publication or abandonment action performed by this worker. `3.0.0` is a candidate version.

## Scope

- Rename Composer identity to `qubrics/mailbox`, require `qubrics/core` `^3.0`, and update all 142 PHP source namespaces, imports and generated type strings.
- Default and server-setting URL: `https://app.qubrics.com/api/v1`. Explicit URL overrides remain supported.
- Package debug version and User-Agent identify `3.0.0`.
- Preserve API method signatures, credential prefixes and existing integration field/header names.
- Update README examples, migration instructions and release guard. Historical changelog entries, evidence and licence attribution remain unchanged.
- CI validates original metadata, installs the candidate core from a temporary Composer manifest, then runs the public seam and syntax checks.

## Red and green evidence

Real dependency installation before the initial test: `composer install --no-interaction --no-progress`, exit 0, installed published `sendmux/core` v2.1.1.

Initial red: `php tests/public-contract.php`, exit 255:

```text
Class "Qubrics\Mailbox\Configuration" not found
```

Candidate dependency setup:

```sh
cp composer.json composer.ci.json
COMPOSER=composer.ci.json composer config repositories.qubrics '{"type":"path","url":".ci/*","options":{"versions":{"qubrics/core":"3.0.0"}}}'
COMPOSER=composer.ci.json composer install --no-interaction --no-progress
composer test
```

Local `.ci/core` points to the sibling core rebrand worktree. Install exited 0 with actual `qubrics/core` 3.0.0 and Composer-generated autoloading. Green `composer test`, exit 0:

```text
Mailbox public contract passed
```

The public test exercises default/server/custom hosts, package identity, API-key auth, generated connection request URL and User-Agent, OAuth factory / bearer middleware / redirect policy, and nested connection / credential deserialisation. Only the external HTTP transport is replaced by Guzzle's existing mock handler. No network request is sent.

`composer validate --strict`, PHP syntax for `src` and `tests`, and `git diff --check`: exit 0. A read-only comparison with HEAD confirmed every PHP source change is an agreed namespace, branding, default-host, middleware-label or version substitution.

Logs: `.claude/artifacts/rebrand-qubrics-php4/{red-composer.log,red.log,green-composer.log,green.log,validate.log,syntax.log}`. Artifacts resolve to the MAIN checkout through the existing symlink. Generated dependency folders and temporary Composer manifests/locks remain untracked.

Changed README/changelog prose passed the parent's independent critique, one humanize pass and protected-set check. Migration notes also identify `Sendmux\Core\SendmuxApiError` → `Qubrics\Core\QubricsApiError`. Local polish record: `.claude/artifacts/rebrand-qubrics-php4/polish-pass.md`.

## Publication boundary

Canonical SDK source is `qubrics/qubrics-sdk` main `2498ae8e90932fa98b54926653a4a6185bb2a4c3`. Its `scripts/generate-php.mjs` still emits Sendmux package/namespace/core identities and replaces generated `src`. Its `scripts/publish-php-split.mjs:40–60` requires SDK/split tree parity and the old Composer identity. Parent coordinates that separate ownership; this change does not bypass either guard.

Current upstream A17 additions were not imported. This source-only candidate preserves its existing integration contract. Remote CI requires the renamed core on main. Publication, registry verification, old-package abandonment and live-host verification remain outside this worker's authority.
