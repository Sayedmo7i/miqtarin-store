# Model Selection & Delegation Guidelines

This document specifies model selection criteria, tier alignment, and subagent delegation rules for MO7I Universal Intelligence.

---

## 1. Model Tier Mapping

When operating in environments with multi-tier model availability, the orchestrator pairs task complexity with the appropriate tier to balance cognitive depth, execution speed, and token budgets:

| Complexity Tier | Characteristics | Recommended Workloads |
| :--- | :--- | :--- |
| **High Reasoning (Pro)** | Deep planning, architectural refactoring, complex bug diagnosis, cross-file domain modeling | Step 1 (Understand), Step 4 (Evaluate & Vet), Step 7 (Plan Acceptance Criteria) |
| **Fast Execution (Flash / Standard)** | Code generation, test running, refactoring against clear spec, documentation | Step 8 (Execute), Step 9 (Verify Tests), Step 11 (Cleanup) |
| **Lightweight (Flash Lite / Fast)** | File lookups, simple regex transforms, status reporting, directory scans | Step 2 (Init Context), Step 3 (List Installed Skills), Step 12 (Status Formatting) |

---

## 2. Delegation & Subagent Invocations

When delegating sub-tasks or launching subagents:
1. **Inherit by Default**: Always specify `inherit` as the subagent model parameter unless the user explicitly commands a specific model or tier.
2. **Never Unauthorized Switch**: An agent must never unilaterally switch tiers or override system defaults based on personal assumptions of task complexity without explicit user instruction.
3. **Dedicated Subagent Scopes**:
   - *Research Subagent*: Read-only exploration, literature searches, codebase surveys.
   - *Self Subagent*: Execution runs requiring matching tool privileges.

---

## 3. Harness & Provider Alignment

- **Respect Host Architecture**: Adhere strictly to the host agent environment.
- **Graceful Capabilities Alignment**: If a model configuration requested in preferences is unavailable in the current provider context, transparently fall back to the host default while documenting the adjustment.
