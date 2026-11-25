# Security, Permissions & Human Gates

This document defines the zero-trust security architecture, threat model, permission boundaries, project isolation, and mandatory human gates enforced by MO7I Universal Intelligence (v2.1.0).

---

## 1. Zero-Trust Autonomous Governance

> **Autonomy must not mean unrestricted destructive access.**

MO7I Universal Intelligence operates under a strict principle of least privilege. While it possesses autonomous reasoning to plan and execute tasks, it enforces rigid sandboxing around side effects that could compromise security, finances, production state, or unrelated project workspaces.

---

## 2. Project Scope Isolation & Anti-Global Policy

During project intelligence and automated skill setup:
1. **Strictly Local Installation**: All project skills are installed into `<projectRoot>/.agents/skills/` and tracked in `skills-lock.json`.
2. **Global Flag Prohibition**: Passing `-g` or `--global` during project setup is strictly prohibited to prevent cross-project pollution.
3. **User Skill Protection**: Pre-existing skills residing in `.agents/skills/` are treated as non-overwritable assets. The setup engine checks for existing skill directories and never overwrites user-authored customizations.
4. **Isolated Metadata**: All project telemetry and profile data are confined to `<projectRoot>/.mo7i/`.

---

## 3. Mandatory Human Approval Gates

The orchestrator MUST halt execution and seek explicit interactive confirmation from the user prior to performing any of the following actions:

1. **Destructive Filesystem Operations**: Commands deleting files outside dedicated temporary directories (`rm -rf`, recursive cleans, directory wipes).
2. **Database Schema Drops**: Destructive migrations, table drops, or truncate operations.
3. **Production Deployments**: Publishing packages to npm/PyPI, deploying cloud workers, or pushing to production environments.
4. **Public Repository Actions**: Creating new public repositories, pushing to public remotes for the first time, or modifying branch protection rules.
5. **Financial Transactions**: Invocations touching payment gateways (Stripe, PayPal, AWS provisioning) or creating billable cloud resources.
6. **Untrusted Skill Installation**: Installing skills from community or unknown authors without enterprise verification.
7. **Privilege Escalation**: Any command attempting to invoke `sudo` or modify system-level permissions.

---

## 4. Secret Protection & Anti-Leakage Controls

- **Zero Secret Transmission**: The orchestrator strictly forbids storing API keys, OAuth tokens, passwords, private SSH keys, or environment secrets in:
  - `.mo7i/project-profile.json`
  - `.mo7i/skill-registry.json`
  - `preferences.json`
  - `skill-knowledge.json`
  - Log transcripts or generated markdown files.
- **Probe Without Logging**: When checking for external API credentials (e.g., `OPENAI_API_KEY`, `STRIPE_SECRET_KEY`), the orchestrator checks variable existence (`test -n "${KEY:-}"`) without logging or outputting the secret value.
- **Automated Scrubbing**: All data serialized to disk by `setup-engine.js` is sanitized via `sanitizeObject()`, redacting any sensitive token matching regex patterns.

---

## 5. Trust Tiers & Vetting Checklist

All discovered skills are vetted against an 8-point inspection checklist prior to execution:
1. *Shell Commands*: Prohibit `curl | sh`, obfuscated eval, or unchecked arguments.
2. *Installation Scripts*: Inspect all files in `scripts/` or `bin/`.
3. *Network Calls*: Verify endpoints against documented public APIs.
4. *Credential Access*: Flag any attempt to access `.env`, `~/.ssh`, or `~/.aws/credentials`.
5. *Filesystem Access*: Restrict writes to project directory and user config space.
6. *Destructive Actions*: Disallow `rm -rf /` or forced git operations.
7. *Telemetry Exfiltration*: Prohibit exfiltrating repository source code.
8. *Privilege Escalation*: Prohibit unauthorized root escalation.
