# Disposable email domains

Snapshot acquired 2026-10-25 from https://disposable.github.io/disposable-email-domains/domains_mx.txt.
Upstream: https://github.com/disposable/disposable-email-domains (MIT; license alongside this file).

The snapshot is shipped with application code, so a fresh deployment blocks listed domains even without a runtime cache. Signup makes no HTTP request to the list provider. The existing `php spark customers:sync-disposable-email-domains` command can add newer entries to the runtime cache; schedule it separately, not in the signup request. Subdomains are also matched. Lists can contain false positives and cannot identify every newly created disposable provider; email OTP remains mandatory.

Do not use the strict list: it also covers legitimate freemail providers. Existing application-specific domain restrictions in SpamAccountCleanupService remain unchanged.
