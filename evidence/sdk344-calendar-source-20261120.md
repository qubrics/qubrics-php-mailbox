# Calendar source sync — mailbox

Goal: Prepare a normal calendar-only mailbox source removal PR under `qubrics/qubrics-sdk#344` rule 12. Source PR only; no merge or publication authorised.

Status: Coded, not merged. One slice fixed locally; PR CI and independent review remain before handback.

## Canonical provenance

- Split base and preserved `origin/archive/calendar`: `534db204f23fd1cabab4d14ed4b9d9a7cfd78e6a`. Archive read only; never created or overwritten.
- Canonical SDK baseline: `086e6c40d2d0fb349d06811120aca504555ab6c6`; its `packages/php/mailbox/src/` blobs exactly match split base `src/` (zero mismatches).
- Canonical SDK final: `4416e3cec4e5cff2440f7aff8cbd6e5cd4b70c5e`, `packages/php/mailbox/src/`.
- Applied committed bytes with `git diff --binary <baseline> <final> -- packages/php/mailbox/src`, then `git apply -p4`. No generated implementation hand edits.
- Canonical ownership: SDK `README.md` Packages table points PHP mailbox source to `packages/php/mailbox`; `scripts/publish-php-split.mjs` consumes this generated source. Publisher not invoked: it creates/pushes tags.
- Final source qualification: all 166 split `src/` files equal immutable canonical Git blobs; zero mismatches and zero calendar filenames/runtime text. The delta removes calendar operations and 22 calendar models. Surviving generated operation blocks can align noisily in the textual diff.
- Composer identity/dependencies, package version, changelog history, README and CI remain byte-for-byte unchanged. No tags, registry writes, release-please, uploads or live endpoint changes.

## Checks

Recorded 2026-11-20 11:28 AEDT. PHP 8.4.10; Composer 2.8.9; unchanged tracked `composer.json`; no tracked lockfile.

- `composer validate --strict`: exit 0.
- Existing CI syntax command over `src/` and `tests/`: exit 0.
- Existing CI candidate dependency setup with temporary `composer.ci.json`, immutable SDK `packages/php/core` archived into `.ci/core`, and `COMPOSER_DISABLE_NETWORK=1`: exit 0 using cached archives. Temporary manifest/lock/vendor are not committed. The generated loader resolves this split's `src/` and root package identity.
- Added behaviour at existing `tests/public-contract.php` seam: public calendar operations and retired model exports are absent. No assertion removed or weakened.
- Red against unchanged split-base `src/`, using `COMPOSER=composer.ci.json composer test`: exit 255, `RuntimeException: Calendar operations are absent from the Mailbox client`.
- Reapplied exact immutable source patch; same command: exit 0, `Mailbox public contract passed`.
- `git diff --check`: exit 0. Metadata/history/workflow diff: empty.

Raw receipts: `/Users/rj/Desktop/GIT-REPOS/sendmux-php-mailbox/.claude/artifacts/sdk344-calendar-source-20261120/` (`canonical.patch`, `composer-validate.log`, `php-syntax.log`, `composer-install-offline.log`, `public-contract-red.log`, `public-contract-green.log`, `exact-source-equality.log`). Equality receipt includes both PHP splits.

## Completion state

Correctness: Immutable generated-source delta only; existing mail APIs retained. This is a major API removal in source and must receive a separate release decision.

Journeys: No live or browser journey; no server/UI change. Unchanged PR CI installs and exercises the actual split on PHP 8.2.

Status: Source PR only. Merge, publication and live calendar backend retirement remain outside this task.

Rollback: Normal revert of the source-sync commit; preserved archive unchanged.

Torn down: No long-lived process or external resource created. Worktree retained for PR review.

Parked: No `check:fast` script exists; existing native metadata, syntax and contract checks compose this task's fast gate. Live retirement waits on `infra-migration1f`; published package bytes unchanged.
