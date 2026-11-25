# Skill Lifecycle Management & Registry Governance

This document specifies the lifecycle classification, persistent registry schemas, atomic storage patterns, and safe cleanup protocols implemented by MO7I Universal Intelligence.

---

## 1. Skill Classifications

Every skill managed by or known to the orchestrator is classified into one of four explicit categories:

| Classification | Definition | Cleanup Policy |
| :--- | :--- | :--- |
| **`core`** | Fundamental infrastructure skills required by the orchestrator (e.g., `find-skills`). | **PERMANENT**. Never automatically removed or pruned under any circumstance. |
| **`pinned`** | Skills explicitly protected or flagged by the user as permanent project or global assets. | **PERMANENT**. Requires explicit, interactive user authorization to remove. |
| **`persistent`** | Multi-session skills used regularly across projects. Retained across workflow runs. | Retained across sessions; eligible for review only during explicit maintenance. |
| **`temporary`** | Installed dynamically to solve a specific sub-task or immediate workflow need. | **ELIGIBLE FOR AUTOMATED CLEANUP** upon task completion if no other project depends on it. |

---

## 2. Persistent Storage Architecture

Persistent skill metadata and audit trails are maintained in:
`~/.config/mo7i/universal-intelligence/` (fallback: `~/.mo7i/universal-intelligence/`)

### File Manifest
- `skill-registry.json`: Canonical inventory of installed skills, scopes, and classifications.
- `skill-knowledge.json`: Operational profiles and capability contracts derived by the learning engine.
- `skill-usage-history.json`: Auditable append-only log of executions, timestamps, and outcomes.
- `skill-compatibility.json`: Matrix of pairwise synergies, sequencing rules, and known conflicts.

---

## 3. Auditable History Schema (`skill-usage-history.json`)

```json
{
  "$schema": "https://json-schema.org/draft/2020-12/schema",
  "version": "1.1.0",
  "history": [
    {
      "session_id": "32eaa9ef-5dd1-430a-a505-e7668ca77635",
      "timestamp": "2026-11-15T18:15:00Z",
      "project_path": "/root/github.com/Sayedmo7i/seha_v2",
      "skill_name": "nextjs-best-practices",
      "action": "LOAD_AND_APPLY",
      "phase": "ROUTING_OPTIMIZATION",
      "status": "PASS",
      "notes": "Applied unstable_cache and server action revalidation patterns."
    }
  ]
}
```

---

## 4. Atomic Write & Recovery Guarantees

All registry writes follow an atomic pattern to prevent file corruption during unexpected crashes:
1. **Pre-Write Backup**: Copy active registry to `<file>.bak`.
2. **Write to Temporary File**: Write new JSON payload to `<file>.tmp.<pid>`.
3. **Atomic Replace**: Atomically rename `<file>.tmp.<pid>` over `<file>`.
4. **Crash Recovery**: If `<file>` is corrupted or invalid JSON, automatically restore from `<file>.bak`.

---

## 5. Safe Automated Cleanup Protocol

At the conclusion of a task, the orchestrator executes automated cleanup:

### Step 1: Candidate Identification
Scan `skill-registry.json` for skills satisfying all criteria:
- `installed_by === "mo7i-universal-intelligence"`
- `type === "temporary"`
- `type !== "core"` AND `type !== "pinned"`

### Step 2: Cross-Project Dependency Check
Before deleting any skill:
- Check whether another active project directory has linked or recorded dependencies on the skill.
- Check `skill-usage-history.json` to verify if the skill is actively used by concurrent sessions.
- **Ambiguity Rule**: If there is any doubt whether another project relies on the skill, **DO NOT REMOVE**. Mark the entry as `"cleanup_candidate": true` and notify the user.

### Step 3: Clean Uninstallation & Verification
1. Execute removal via Skills CLI:
   ```bash
   # For project scope:
   npx skills remove <name> -y

   # For global scope:
   npx skills remove <name> -g -y
   ```
2. Verify filesystem removal (confirm `.agents/skills/<name>` no longer exists).
3. Update `skill-registry.json` and append cleanup entry to `skill-usage-history.json`.
4. Report removal in the task summary.
