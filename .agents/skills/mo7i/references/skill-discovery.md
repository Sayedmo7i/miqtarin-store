# Skill Discovery & Candidate Selection

This document specifies the discovery workflows, candidate scoring matrices, collection identification, and selection algorithms implemented by MO7I Universal Intelligence (v2.1.0).

---

## 1. Mandatory `find-skills` Integration

MO7I Universal Intelligence designates `find-skills` (`vercel-labs/skills`) as a **CORE mandatory dependency**. Rather than maintaining a brittle internal catalog, the orchestrator connects directly to the open agent skills ecosystem via `find-skills` and [skills.sh](https://skills.sh).

### The Discovery Triggers
1. **Project Setup Trigger**: At Phase 5 of the execution lifecycle, `mo7i-project-setup` analyzes project dependency manifests (`package.json`, `pubspec.yaml`, `composer.json`, etc.) and queries `skills.sh` for foundational stack specialists and collection packages.
2. **Task-Specific Trigger**: At Phase 8 of the execution lifecycle, if an immediate engineering task requires niche expertise beyond project foundational skills, the orchestrator discovers temporary specialist skills.

---

## 2. Discovery Workflow

```
   [1. Formulate Query Keywords]
                 │
                 ▼
   [2. Execute npx skills find] ──────► [No Results] ──► [Broaden Keywords]
                 │                                               │
                 ▼                                               ▼
   [3. Fetch Candidate Pool] ◄───────────────────────────────────┘
                 │
                 ▼
   [4. Inspect Source & Metadata]
                 │
                 ▼
   [5. Score Across 5 Dimensions]
                 │
                 ▼
   [6. Select Primary Specialist or Collection]
```

### Search Query Engineering
Queries must target the specific framework, runtime, and objective:
- **Project Discovery Queries**: `"nextjs best practices"`, `"flutter architecture"`, `"laravel eloquet"`, `"matt pocock"`
- **Task Discovery Queries**: `"pixijs rendering"`, `"auth0 authentication"`, `"playwright e2e"`
- **Suboptimal Queries**: `"how to code"`, `"make site fast"`, `"fix bugs"`

---

## 3. The 5-Dimension Scoring Matrix

Candidates are evaluated across an objective 100-point rubric:

| Dimension | Weight | Evaluation Criteria |
| :--- | :---: | :--- |
| **Task Relevance & Stack Fit** | **35%** | Direct alignment with the project's exact framework, major version, and language runtime. Penalizes mismatched major versions. |
| **Publisher Reputation** | **25%** | Prioritizes official vendor and verified creator organizations (`mattpocock`, `vercel-labs`, `anthropics`, `cloudflare`, `auth0`) over unverified forks. |
| **Community Adoption** | **15%** | Install counts and GitHub stars on skills.sh indicate battle-tested community usage. |
| **Documentation Quality** | **15%** | Clear, concise `SKILL.md` with explicit triggers, structured workflows, minimal bloat, and modular references. |
| **Security & Maintainability** | **10%** | Clean automated audit reports (Gen, Socket, Snyk Safe badges on skills.sh) and absence of risky runtime shell scripts. |

---

## 4. Collection Discovery: Matt Pocock Skills

When discovering curated collections (such as the required Matt Pocock collection):
1. Query via `npx skills find 'matt pocock'`.
2. Inspect official GitHub repository (`https://github.com/mattpocock/skills.git`) and Skills.sh catalog (`https://skills.sh/mattpocock/skills`).
3. Enumerate all skills using non-interactive list mode: `npx skills add mattpocock/skills -l`.
4. Parse and verify each candidate: 38 verified skills active, with 1 skipped upstream (`software-factory` due to YAML description syntax error).
5. Install at **project scope** (`npx skills add mattpocock/skills --skill <name> -y`) without `-g`.

---

## 5. Single Primary Specialist Rule

To prevent instruction collisions and context bloat, the orchestrator selects **ONE** primary specialist skill per domain stage. Secondary skills are only introduced when a multi-domain workflow transitions to a different technical stage (e.g., transition from database schema design to frontend UI implementation).

---

## 6. Graceful Fallback (No Match Found)

If `npx skills find` returns 0 candidates, or if all candidates fail the minimum quality or security threshold:
1. Acknowledge the absence clearly in the turn:
   > *"No specialist skill found on skills.sh matching '<query>'. Proceeding using direct foundation agent capabilities."*
2. Proceed immediately with native tool and reasoning capabilities.
3. **Never install irrelevant or low-quality skills simply to satisfy an install step.**
