# Skill Learning Engine & Knowledge State Transitions

This document specifies the operational learning engine, capability profiling, and explicit skill state machine implemented by MO7I Universal Intelligence.

---

## 1. The Six States of Skill Knowledge

To prevent premature assumptions of capability, the orchestrator tracks every skill across six distinct states:

```
[1. DISCOVERED] ──► [2. INSTALLED] ──► [3. UNDERSTOOD] ──► [4. RUNTIME_VERIFIED]
                                                                     │
                               ┌─────────────────────────────────────┴─────────────────────────────────────┐
                               ▼                                                                           ▼
                   [5. SUCCESSFULLY_USED]                                                       [6. FAILED_OR_INCOMPATIBLE]
```

### State Definitions
1. **`DISCOVERED`**: Skill candidate identified on skills.sh or local storage via `find-skills`. Scored but not yet downloaded or vetted.
2. **`INSTALLED`**: Skill files exist in `.agents/skills/<name>`. Not yet analyzed or loaded into memory.
3. **`UNDERSTOOD`**: `SKILL.md` and referenced guides have been fully read. An operational profile detailing capabilities, limitations, and input/output contracts has been synthesized.
4. **`RUNTIME_VERIFIED`**: Underlying compilers, CLI tools, and runtime dependencies have been verified on the host system via active probes (e.g. `which <binary>`).
5. **`SUCCESSFULLY_USED`**: Skill was actively applied in a workflow, guided code/tool operations, and produced verifiable passing results.
6. **`FAILED_OR_INCOMPATIBLE`**: Skill execution produced unrecoverable errors, failed security inspection, or prescribed conventions conflicting with higher-precedence project rules.

*Anti-Hallucination Invariant: Never mark a skill as `RUNTIME_VERIFIED` or `SUCCESSFULLY_USED` merely because its files are `INSTALLED`.*

---

## 2. The Four-Phase Learning Loop

```
[Phase A: Inspection] ──► [Phase B: Understanding] ──► [Phase C: Integration] ──► [Phase D: Practical Validation]
   - Read SKILL.md           - Capabilities Profile       - Task Mapping             - Environment Probe
   - Inspect References      - Tool/Command Needs         - Complementary Skills     - Dependency Check
   - Scan Scripts            - Input/Output Contract      - Conflict Identification   - Execution Test
   - Review Permissions      - Operational Limits         - Execution Ordering       - Result Verification
```

---

## 3. Skill Understanding Record Schema

Operational profiles are maintained in `~/.config/mo7i/universal-intelligence/skill-knowledge.json`:

```json
{
  "$schema": "https://json-schema.org/draft/2020-12/schema",
  "version": "2.0.0",
  "skills": {
    "nextjs-best-practices": {
      "name": "nextjs-best-practices",
      "state": "SUCCESSFULLY_USED",
      "version": "1.0.0",
      "source": "sickn33/agentic-awesome-skills@nextjs-best-practices",
      "publisher": "sickn33",
      "install_path": "/workspace/.agents/skills/nextjs-best-practices",
      "primary_capabilities": [
        "Next.js App Router route handlers",
        "Server Components data fetching",
        "Tag-based cache revalidation"
      ],
      "supported_task_types": ["routing", "caching", "ssr", "optimization"],
      "required_dependencies": ["node", "npm", "next"],
      "available_tools": ["run_command", "view_file", "replace_file_content"],
      "required_inputs": ["next.config.js", "app/ directory"],
      "expected_outputs": ["optimized cache headers", "valid build artifacts"],
      "limitations": ["App Router only; incompatible with Pages Router"],
      "security_tier": "Trusted",
      "complementary_skills": ["tailwind-design", "prisma-database"],
      "conflicting_skills": ["pages-router-legacy"],
      "last_successful_usage": "2026-11-15T18:30:00Z"
    }
  }
}
```
