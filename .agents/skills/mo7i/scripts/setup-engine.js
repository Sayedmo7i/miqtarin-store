#!/usr/bin/env node

/**
 * MO7I Project Intelligence & Automated Skill Setup Engine
 * Version: 2.3.0
 * 
 * Provides automated project analysis, dynamic specialist skill discovery,
 * integration with the official Matt Pocock collection, strictly project-scoped
 * installation, post-install setup detection & lifecycle tracking, human-gate
 * interaction enforcement, and persistent skill registry management.
 */

const fs = require('fs');
const path = require('path');
const cp = require('child_process');
const net = require('net');

const ENGINE_VERSION = '2.5.0';

// Official verified Matt Pocock skills collection (from https://skills.sh/mattpocock/skills)
const MATT_POCOCK_OFFICIAL_SOURCE = {
  publisher: 'Matt Pocock (mattpocock)',
  repository: 'https://github.com/mattpocock/skills.git',
  skillsShUrl: 'https://skills.sh/mattpocock/skills',
  packageId: 'mattpocock/skills',
  totalDiscovered: 39,
  validCount: 38,
  skippedCount: 1,
  skippedDetails: [
    {
      skill: 'software-factory',
      path: 'skills/in-progress/software-factory/SKILL.md',
      reason: 'Upstream YAML syntax error (nested mapping without indentation in frontmatter description)'
    }
  ],
  skills: [
    { id: 'ask-matt', category: 'DEVELOPMENT', description: 'Ask which skill or flow fits your situation. A router over skills.' },
    { id: 'code-review', category: 'QUALITY_ASSURANCE', description: 'Review changes along Standards and Spec axes with parallel sub-agents.' },
    { id: 'codebase-design', category: 'DEVELOPMENT', description: 'Shared vocabulary for designing deep modules with hidden implementation.' },
    { id: 'diagnosing-bugs', category: 'QUALITY_ASSURANCE', description: 'Systematic diagnosis loop for hard bugs and regressions.' },
    { id: 'domain-modeling', category: 'DEVELOPMENT', description: 'Build and sharpen domain models, GLOSSARY.md, and ADRs.' },
    { id: 'grill-with-docs', category: 'DEVELOPMENT', description: 'Relentless interview creating ADRs and glossary as we go.' },
    { id: 'implement', category: 'DEVELOPMENT', description: 'Implement a piece of work based on a spec or set of tickets.' },
    { id: 'implement-spec', category: 'DEVELOPMENT', description: 'Implement the result of /to-spec and /to-tickets in code.' },
    { id: 'improve-codebase-architecture', category: 'DEVELOPMENT', description: 'Scan codebase for deepening opportunities and present visual report.' },
    { id: 'pr', category: 'DEVELOPMENT', description: 'Write structured, evidence-rich PR descriptions.' },
    { id: 'prototype', category: 'DEVELOPMENT', description: 'Build throwaway prototype to validate state model or UX.' },
    { id: 'research', category: 'DEVELOPMENT', description: 'Investigate a question against high-trust primary sources.' },
    { id: 'retro', category: 'DEVELOPMENT', description: 'Conduct structured retrospective on a coding session.' },
    { id: 'setup-matt-pocock-skills', category: 'DEVELOPMENT', description: 'Configure repository issue tracker, labels, and domain doc layout.' },
    { id: 'tdd', category: 'QUALITY_ASSURANCE', description: 'Test-driven development (red-green-refactor) workflow.' },
    { id: 'to-spec', category: 'DEVELOPMENT', description: 'Turn current conversation into a technical specification.' },
    { id: 'to-tickets', category: 'DEVELOPMENT', description: 'Break plans and specs into tracer-bullet tickets with dependency edges.' },
    { id: 'triage', category: 'DEVELOPMENT', description: 'State-machine triage for issues and PRs.' },
    { id: 'wayfinder', category: 'DEVELOPMENT', description: 'Map out large milestones as decision tickets on issue tracker.' },
    { id: 'wizard', category: 'DEVELOPMENT', description: 'Generate interactive bash wizard for human-only steps.' },
    { id: 'grill-me', category: 'DEVELOPMENT', description: 'Relentless interview to stress-test plans or architecture.' },
    { id: 'grilling', category: 'DEVELOPMENT', description: 'Relentless grilling triggers on decisions.' },
    { id: 'handoff', category: 'DEVELOPMENT', description: 'Compact conversation into handoff document for another agent.' },
    { id: 'teach', category: 'DEVELOPMENT', description: 'Teach the user a new skill or concept.' },
    { id: 'to-questionnaire', category: 'DEVELOPMENT', description: 'Turn complex decisions into questionnaire for stakeholders.' },
    { id: 'wait-what', category: 'DEVELOPMENT', description: 'Stop and repitch concepts that failed to land clearly.' },
    { id: 'writing-for-agents', category: 'DEVELOPMENT', description: 'Format documentation specifically for autonomous AI agents.' },
    { id: 'chief-of-staff', category: 'DEVELOPMENT', description: 'Pursue long-running goal by coordinating sub-agents.' },
    { id: 'claude-handoff', category: 'DEVELOPMENT', description: 'Hand conversation off to fresh background agent.' },
    { id: 'loop-me', category: 'DEVELOPMENT', description: 'Interactive specification interview for workflows.' },
    { id: 'setup-ts-deep-modules', category: 'PROJECT_SPECIFIC', description: 'Enforce TypeScript deep module boundaries via dependency-cruiser.' },
    { id: 'writing-beats', category: 'DEVELOPMENT', description: 'Ground narrative beats sequentially.' },
    { id: 'writing-fragments', category: 'DEVELOPMENT', description: 'Explore unshaped conceptual fragments.' },
    { id: 'writing-shape', category: 'DEVELOPMENT', description: 'Shape raw material into structured articles.' },
    { id: 'git-guardrails-claude-code', category: 'QUALITY_ASSURANCE', description: 'Install hooks blocking dangerous git commands.' },
    { id: 'migrate-to-shoehorn', category: 'PROJECT_SPECIFIC', description: 'Migrate test assertions to @total-typescript/shoehorn.' },
    { id: 'scaffold-exercises', category: 'OPTIONAL', description: 'Scaffold interactive exercise directory structures.' },
    { id: 'setup-pre-commit', category: 'QUALITY_ASSURANCE', description: 'Set up Husky pre-commit hooks with lint-staged and tests.' }
  ]
};

/**
 * Audit and sanitize any object to ensure no credentials or secrets are persisted.
 */
function sanitizeObject(obj) {
  if (!obj || typeof obj !== 'object') return obj;
  if (Array.isArray(obj)) return obj.map(sanitizeObject);
  
  const sanitized = {};
  const secretPattern = /(key|secret|token|password|auth|bearer|credential|cert|private)/i;
  
  for (const [key, value] of Object.entries(obj)) {
    if (secretPattern.test(key) && typeof value === 'string' && value.length > 8) {
      sanitized[key] = '[REDACTED_BY_MO7I_SECURITY]';
    } else if (typeof value === 'object' && value !== null) {
      sanitized[key] = sanitizeObject(value);
    } else {
      sanitized[key] = value;
    }
  }
  return sanitized;
}

/**
 * Scan text content or script for dangerous or malicious patterns.
 */
function scanContentSecurity(content) {
  const violations = [];
  if (typeof content !== 'string') return { safe: true, violations };

  const blocked = [
    { pattern: /curl\s+[^|\n]+\|\s*(ba|z)?sh/i, reason: 'Arbitrary remote shell execution via pipe' },
    { pattern: /wget\s+[^|\n]+\|\s*(ba|z)?sh/i, reason: 'Arbitrary remote shell execution via wget' },
    { pattern: /rm\s+-rf\s+(\/|~|\$HOME|\.\.)/i, reason: 'Destructive deletion targeting root, home, or parent' },
    { pattern: />\s*\/etc\/(passwd|shadow|hosts)/i, reason: 'Attempt to overwrite sensitive system configuration' },
    { pattern: /chmod\s+777\s+(\/|~|\$HOME)/i, reason: 'Insecure permissive file permissions at root or home' }
  ];

  for (const rule of blocked) {
    if (rule.pattern.test(content)) {
      violations.push(rule.reason);
    }
  }

  return {
    safe: violations.length === 0,
    violations
  };
}

/**
 * Dynamic port probing utility.
 * Finds the first open TCP port starting at startPort.
 */
function findAvailablePort(startPort = 3000, maxAttempts = 50) {
  return new Promise((resolve, reject) => {
    let port = startPort;
    function tryPort(p) {
      const server = net.createServer();
      server.unref();
      server.on('error', (err) => {
        if (err.code === 'EADDRINUSE') {
          if (p - startPort >= maxAttempts) {
            reject(new Error(`No open port found after ${maxAttempts} attempts starting at ${startPort}`));
          } else {
            tryPort(p + 1);
          }
        } else {
          reject(err);
        }
      });
      server.listen(p, () => {
        server.close(() => resolve(p));
      });
    }
    tryPort(port);
  });
}

/**
 * Synchronous port availability check.
 */
function findAvailablePortSync(startPort = 3000, maxAttempts = 50) {
  for (let port = startPort; port < startPort + maxAttempts; port++) {
    try {
      const out = cp.execSync(`node -e '
        const net = require("net");
        const s = net.createServer();
        s.once("error", () => process.exit(1));
        s.listen(${port}, () => { s.close(() => process.exit(0)); });
      '`, { stdio: 'pipe' });
      return port;
    } catch {
      // Port in use, continue
    }
  }
  return startPort;
}

/**
 * Load the curated post-install setup map with safe fallbacks.
 */
function loadPostInstallSetupMap() {
  const mapPath = path.resolve(__dirname, '../../../references/post-install-setup-map.json');
  if (fs.existsSync(mapPath)) {
    try {
      return JSON.parse(fs.readFileSync(mapPath, 'utf8'));
    } catch {
      // fallback
    }
  }
  return {
    collections: {
      'mattpocock/skills': {
        collection: 'mattpocock/skills',
        setupSkill: 'setup-matt-pocock-skills',
        interactive: true,
        description: 'Configures issue tracker, triage labels, and domain doc layout for Matt Pocock engineering skills.',
        writes: ['docs/agents/issue-tracker.md', 'docs/agents/domain.md', 'CLAUDE.md', 'AGENTS.md'],
        verification: {
          files: ['docs/agents/issue-tracker.md', 'docs/agents/domain.md'],
          contentChecks: [{ files: ['CLAUDE.md', 'AGENTS.md'], pattern: '## Agent skills' }]
        }
      },
      'setup-pre-commit': {
        collection: 'mattpocock/skills',
        setupSkill: 'setup-pre-commit',
        interactive: false,
        description: 'Sets up Husky pre-commit hooks with lint-staged (Prettier), type checking, and tests.',
        writes: ['.husky/pre-commit', '.lintstagedrc', '.prettierrc', 'package.json'],
        verification: {
          files: ['.husky/pre-commit', '.lintstagedrc']
        }
      },
      'setup-ts-deep-modules': {
        collection: 'mattpocock/skills',
        setupSkill: 'setup-ts-deep-modules',
        interactive: true,
        description: 'Enforces deep module boundaries via dependency-cruiser rules.',
        writes: ['.dependency-cruiser.cjs', 'package.json'],
        verification: {
          files: ['.dependency-cruiser.cjs']
        }
      }
    }
  };
}

/**
 * Inspects repository artifacts and determines the project stack archetype.
 */
function analyzeProject(projectDir = process.cwd()) {
  const resolvedDir = path.resolve(projectDir);
  if (!fs.existsSync(resolvedDir)) {
    throw new Error(`Project directory not found: ${resolvedDir}`);
  }
  const profile = {
    id: path.basename(resolvedDir),
    path: resolvedDir,
    type: 'Generic Software Project',
    languages: [],
    frameworks: [],
    architecture: 'Modular Monolith / Standard',
    databases: [],
    testFrameworks: [],
    codeQualityConventions: [],
    existingTools: [],
    existingSkills: [],
    deployment: 'Local',
    inspectedFiles: [],
    setupStatus: 'UNINITIALIZED'
  };

  const fileExists = (rel) => fs.existsSync(path.join(resolvedDir, rel));
  const readJsonSafe = (rel) => {
    try {
      const full = path.join(resolvedDir, rel);
      if (fs.existsSync(full)) {
        profile.inspectedFiles.push(rel);
        return JSON.parse(fs.readFileSync(full, 'utf8'));
      }
    } catch {
      // ignore
    }
    return null;
  };
  const readFileSafe = (rel) => {
    try {
      const full = path.join(resolvedDir, rel);
      if (fs.existsSync(full)) {
        profile.inspectedFiles.push(rel);
        return fs.readFileSync(full, 'utf8');
      }
    } catch {
      // ignore
    }
    return null;
  };

  // Inspect existing agent instructions
  if (fileExists('AGENTS.md')) profile.existingTools.push('AGENTS.md');
  if (fileExists('CLAUDE.md')) profile.existingTools.push('CLAUDE.md');
  if (fileExists('README.md')) profile.existingTools.push('README.md');

  // Discover skills on disk in project scope
  const projectSkillsDirs = ['.agents/skills', '.gemini/skills', '.skills'];
  for (const sDir of projectSkillsDirs) {
    const fullDir = path.join(resolvedDir, sDir);
    if (fs.existsSync(fullDir)) {
      try {
        const entries = fs.readdirSync(fullDir, { withFileTypes: true });
        for (const entry of entries) {
          if (entry.isDirectory() && !profile.existingSkills.includes(entry.name)) {
            profile.existingSkills.push(entry.name);
          }
        }
      } catch {
        // ignore
      }
    }
  }

  // Discover skills from lockfile
  const skillsLock = readJsonSafe('skills-lock.json');
  if (skillsLock && skillsLock.skills) {
    for (const sName of Object.keys(skillsLock.skills)) {
      if (!profile.existingSkills.includes(sName)) {
        profile.existingSkills.push(sName);
      }
    }
  }


  // Inspect Node / JavaScript / TypeScript ecosystem
  const pkg = readJsonSafe('package.json');
  if (pkg) {
    const deps = { ...(pkg.dependencies || {}), ...(pkg.devDependencies || {}) };

    if (deps['typescript'] || fileExists('tsconfig.json')) {
      if (!profile.languages.includes('TypeScript')) profile.languages.push('TypeScript');
    }
    if (!profile.languages.includes('JavaScript')) profile.languages.push('JavaScript');

    // Framework detection
    if (deps['next']) {
      profile.type = 'Fullstack Web Application (Next.js)';
      profile.frameworks.push({ name: 'Next.js', version: deps['next'] });
      profile.deployment = 'Vercel / Cloud Container';
      profile.architecture = (fileExists('app') || fileExists('src/app'))
        ? 'Next.js App Router (Server Actions & Components)'
        : 'Next.js Pages Router';
    } else if (deps['react']) {
      profile.type = 'Frontend Web Application (React)';
      profile.frameworks.push({ name: 'React', version: deps['react'] });
    } else if (deps['vue'] || deps['nuxt']) {
      profile.type = 'Web Application (Vue/Nuxt)';
      profile.frameworks.push({ name: deps['nuxt'] ? 'Nuxt' : 'Vue', version: deps['nuxt'] || deps['vue'] });
    } else if (deps['@angular/core']) {
      profile.type = 'Web Application (Angular)';
      profile.frameworks.push({ name: 'Angular', version: deps['@angular/core'] });
    } else if (deps['express'] || deps['fastify'] || deps['koa'] || deps['@nestjs/core']) {
      profile.type = 'Backend Web Service (Node.js)';
      if (deps['@nestjs/core']) profile.frameworks.push({ name: 'NestJS', version: deps['@nestjs/core'] });
      if (deps['express']) profile.frameworks.push({ name: 'Express', version: deps['express'] });
      if (deps['fastify']) profile.frameworks.push({ name: 'Fastify', version: deps['fastify'] });
    }

    // Styling & UI
    if (deps['tailwindcss'] || fileExists('tailwind.config.js') || fileExists('tailwind.config.ts')) {
      profile.frameworks.push({ name: 'Tailwind CSS', version: deps['tailwindcss'] || 'detected' });
    }

    // Databases & ORMs
    if (deps['@prisma/client'] || deps['prisma'] || fileExists('prisma/schema.prisma')) {
      profile.databases.push('Prisma ORM');
    }
    if (deps['drizzle-orm']) profile.databases.push('Drizzle ORM');
    if (deps['typeorm']) profile.databases.push('TypeORM');
    if (deps['mongoose']) profile.databases.push('MongoDB / Mongoose');

    // Testing
    if (deps['vitest'] || fileExists('vitest.config.ts')) profile.testFrameworks.push('Vitest');
    if (deps['jest'] || fileExists('jest.config.js')) profile.testFrameworks.push('Jest');
    if (deps['playwright'] || deps['@playwright/test']) profile.testFrameworks.push('Playwright');
    if (deps['cypress']) profile.testFrameworks.push('Cypress');

    // Code Quality
    if (deps['eslint'] || fileExists('.eslintrc.json') || fileExists('eslint.config.js') || fileExists('eslint.config.mjs')) {
      profile.codeQualityConventions.push('ESLint');
    }
    if (deps['prettier'] || fileExists('.prettierrc')) {
      profile.codeQualityConventions.push('Prettier');
    }
    if (deps['husky'] || fileExists('.husky')) {
      profile.existingTools.push('Husky Git Hooks');
    }
  }

  // Inspect Flutter / Dart ecosystem
  const pubspecRel = fileExists('pubspec.yaml') ? 'pubspec.yaml' : (fileExists('mobile-apps/customer/pubspec.yaml') ? 'mobile-apps/customer/pubspec.yaml' : null);
  const pubspec = pubspecRel ? readFileSafe(pubspecRel) : null;
  if (pubspec) {
    profile.type = 'Mobile Application (Flutter)';
    if (!profile.languages.includes('Dart')) profile.languages.push('Dart');
    profile.frameworks.push({ name: 'Flutter SDK', version: 'detected' });
    profile.deployment = 'iOS App Store & Google Play';

    if (/flutter_bloc:|bloc:/i.test(pubspec)) {
      profile.architecture = 'Flutter BLoC Pattern';
    } else if (/riverpod:|flutter_riverpod:/i.test(pubspec)) {
      profile.architecture = 'Flutter Riverpod State Architecture';
    } else if (/provider:/i.test(pubspec)) {
      profile.architecture = 'Flutter Provider Architecture';
    } else {
      profile.architecture = 'Flutter Reactive State Model';
    }

    if (/flutter_test:/i.test(pubspec)) {
      profile.testFrameworks.push('flutter_test');
    }
    if (/hive:|isar:|sqflite:/i.test(pubspec)) {
      profile.databases.push('Mobile Local Embedded Storage (SQLite/Hive/Isar)');
    }
    profile.codeQualityConventions.push('analysis_options.yaml Dart Lint');
  }

  // Inspect PHP / Laravel ecosystem
  const composerRel = fileExists('composer.json') ? 'composer.json' : (fileExists('backend/composer.json') ? 'backend/composer.json' : null);
  const composer = composerRel ? readJsonSafe(composerRel) : null;
  if (composer || fileExists('artisan') || fileExists('backend/artisan')) {
    const cDeps = composer ? { ...(composer.require || {}), ...(composer['require-dev'] || {}) } : {};
    if (!profile.languages.includes('PHP')) profile.languages.push('PHP');

    if (cDeps['laravel/framework'] || fileExists('artisan') || fileExists('backend/artisan')) {
      if (pubspec) {
        profile.type = 'Fullstack Multi-Platform Ecosystem (Laravel API & Web + Flutter Mobile Apps)';
      } else {
        profile.type = 'Backend Web Application / API (Laravel)';
      }
      profile.frameworks.push({ name: 'Laravel', version: cDeps['laravel/framework'] || 'detected' });
      profile.architecture = 'Laravel MVC & Eloquent Service Architecture' + (pubspec ? ' + Flutter Mobile Clients' : '');
      profile.databases.push('Laravel Eloquent ORM (MySQL / PostgreSQL / SQLite)');
    } else if (cDeps['symfony/framework-bundle']) {
      profile.type = 'Web Application (Symfony)';
      profile.frameworks.push({ name: 'Symfony', version: cDeps['symfony/framework-bundle'] });
    }

    if (cDeps['phpunit/phpunit']) profile.testFrameworks.push('PHPUnit');
    if (cDeps['pestphp/pest']) profile.testFrameworks.push('Pest PHP');
    if (cDeps['friendsofphp/php-cs-fixer']) profile.codeQualityConventions.push('PHP CS Fixer');
    if (cDeps['phpstan/phpstan']) profile.codeQualityConventions.push('PHPStan');
  }

  // Inspect Python ecosystem
  if (fileExists('requirements.txt') || fileExists('pyproject.toml') || fileExists('Pipfile')) {
    if (!profile.languages.includes('Python')) profile.languages.push('Python');
    const pyReq = readFileSafe('requirements.txt') || '';
    const pyProj = readFileSafe('pyproject.toml') || '';
    const pyCombined = pyReq + '\n' + pyProj;

    if (/django/i.test(pyCombined)) {
      profile.type = 'Web Application (Django)';
      profile.frameworks.push({ name: 'Django', version: 'detected' });
      profile.databases.push('Django ORM');
    } else if (/fastapi/i.test(pyCombined)) {
      profile.type = 'Web API Service (FastAPI)';
      profile.frameworks.push({ name: 'FastAPI', version: 'detected' });
    } else if (/flask/i.test(pyCombined)) {
      profile.type = 'Web Application (Flask)';
      profile.frameworks.push({ name: 'Flask', version: 'detected' });
    } else if (/torch|tensorflow|transformers|scikit-learn/i.test(pyCombined)) {
      profile.type = 'Machine Learning / Data Intelligence';
      profile.frameworks.push({ name: 'PyTorch/ML Frameworks', version: 'detected' });
    }

    if (/pytest/i.test(pyCombined)) profile.testFrameworks.push('Pytest');
    if (/ruff/i.test(pyCombined)) profile.codeQualityConventions.push('Ruff');
    if (/mypy/i.test(pyCombined)) profile.codeQualityConventions.push('Mypy');
  }

  // Inspect Rust ecosystem
  if (fileExists('Cargo.toml')) {
    if (!profile.languages.includes('Rust')) profile.languages.push('Rust');
    profile.type = 'Systems / Application Service (Rust)';
    profile.architecture = 'Rust Modular Crate Architecture';
    profile.inspectedFiles.push('Cargo.toml');
  }

  // Inspect Go ecosystem
  if (fileExists('go.mod')) {
    if (!profile.languages.includes('Go')) profile.languages.push('Go');
    profile.type = 'Backend Service (Go)';
    profile.architecture = 'Go Standard Package Architecture';
    profile.inspectedFiles.push('go.mod');
  }

  // Inspect Docker & DevOps
  if (fileExists('Dockerfile') || fileExists('docker-compose.yml') || fileExists('docker-compose.yaml')) {
    profile.existingTools.push('Docker Containerization');
    if (profile.deployment === 'Local') profile.deployment = 'Docker Container';
  }

  // Inspect CI/CD workflows
  const ghWorkflows = path.join(resolvedDir, '.github/workflows');
  if (fs.existsSync(ghWorkflows)) {
    try {
      const wFiles = fs.readdirSync(ghWorkflows);
      if (wFiles.length > 0) {
        profile.existingTools.push(`GitHub Actions (${wFiles.length} workflows)`);
        profile.inspectedFiles.push('.github/workflows');
      }
    } catch {
      // ignore
    }
  }

  // Non-Technical / General Workspace detection
  if (profile.languages.length === 0 && profile.frameworks.length === 0) {
    let hasCodeFiles = false;
    try {
      const files = fs.readdirSync(resolvedDir);
      for (const f of files) {
        if (/\.(js|ts|jsx|tsx|py|go|rs|rb|php|java|c|cpp|cs|swift|kt|dart)$/i.test(f)) {
          hasCodeFiles = true;
          break;
        }
      }
    } catch {
      // ignore
    }

    if (!hasCodeFiles) {
      profile.type = 'Non-Technical / General Workspace';
      profile.architecture = 'Document & Research Workspace';
    }
  }

  // Check if existing MO7I project intelligence profile exists
  const mo7iProfilePath = path.join(resolvedDir, '.mo7i', 'project-profile.json');
  if (fs.existsSync(mo7iProfilePath)) {
    try {
      const existingMo7i = JSON.parse(fs.readFileSync(mo7iProfilePath, 'utf8'));
      profile.setupStatus = existingMo7i.setupStatus || 'INITIALIZED';
      profile.lastAuditAt = existingMo7i.lastAuditAt || existingMo7i.analyzedAt;
    } catch {
      profile.setupStatus = 'CORRUPTED_PROFILE';
    }
  }

  return sanitizeObject(profile);
}

/**
 * Categorize skills according to the 6 specified project setup categories.
 */
function recommendProjectSkills(profile) {
  const categories = {
    CORE_PROJECT_SKILLS: [],
    DEVELOPMENT_SKILLS: [],
    QUALITY_ASSURANCE: [],
    PROJECT_SPECIFIC_SPECIALISTS: [],
    MATT_POCOCK_COLLECTION: [],
    OPTIONAL_SKILLS: []
  };

  const isNonTechnical = profile.type && (profile.type.includes('Non-Technical') || (profile.languages.length === 0 && profile.frameworks.length === 0));
  const isTypeScript = profile.languages && profile.languages.includes('TypeScript');
  const isPython = profile.languages && (profile.languages.includes('Python') || profile.type.includes('FastAPI') || profile.type.includes('Django') || profile.type.includes('Flask'));

  // Matt Pocock collection integration with archetype filtering
  for (const skill of MATT_POCOCK_OFFICIAL_SOURCE.skills) {
    const item = {
      id: skill.id,
      package: MATT_POCOCK_OFFICIAL_SOURCE.packageId,
      source: 'mattpocock/skills',
      category: skill.category,
      description: skill.description,
      installed: profile.existingSkills.includes(skill.id)
    };

    if (isNonTechnical) {
      // In non-technical projects, only include documentation, research, and non-coding collaboration skills
      const nonTechnicalAllowed = [
        'research',
        'writing-for-agents',
        'writing-shape',
        'writing-beats',
        'writing-fragments',
        'to-questionnaire',
        'teach',
        'handoff',
        'grill-me',
        'ask-matt',
        'wait-what'
      ];
      if (nonTechnicalAllowed.includes(skill.id)) {
        categories.MATT_POCOCK_COLLECTION.push(item);
        if (skill.category === 'DEVELOPMENT') {
          categories.DEVELOPMENT_SKILLS.push(item);
        } else if (skill.category === 'OPTIONAL') {
          categories.OPTIONAL_SKILLS.push(item);
        }
      }
      continue;
    }

    // For technical projects: exclude TypeScript-specific specialists unless TypeScript is in languages
    if (skill.category === 'PROJECT_SPECIFIC') {
      if ((skill.id === 'setup-ts-deep-modules' || skill.id === 'migrate-to-shoehorn') && !isTypeScript) {
        continue;
      }
    }

    categories.MATT_POCOCK_COLLECTION.push(item);

    if (skill.category === 'DEVELOPMENT') {
      categories.DEVELOPMENT_SKILLS.push(item);
    } else if (skill.category === 'QUALITY_ASSURANCE') {
      categories.QUALITY_ASSURANCE.push(item);
    } else if (skill.category === 'OPTIONAL') {
      categories.OPTIONAL_SKILLS.push(item);
    }
  }

  // Core & Project-Specific skills based on stack
  if (isNonTechnical) {
    categories.CORE_PROJECT_SKILLS.push({
      id: 'research',
      package: 'mattpocock/skills@research',
      source: 'mattpocock/skills',
      description: 'Investigate questions against high-trust primary sources with grounded evidence.',
      installed: profile.existingSkills.includes('research')
    });
    categories.CORE_PROJECT_SKILLS.push({
      id: 'writing-for-agents',
      package: 'mattpocock/skills@writing-for-agents',
      source: 'mattpocock/skills',
      description: 'Format documentation and knowledge artifacts specifically for AI agents.',
      installed: profile.existingSkills.includes('writing-for-agents')
    });
  } else if (profile.type.includes('Next.js')) {
    categories.CORE_PROJECT_SKILLS.push({
      id: 'nextjs-best-practices',
      package: 'sickn33/agentic-awesome-skills@nextjs-best-practices',
      source: 'skills.sh',
      description: 'Next.js App Router, Server Actions, route handlers, and SSR caching standards.',
      installed: profile.existingSkills.includes('nextjs-best-practices')
    });
    categories.CORE_PROJECT_SKILLS.push({
      id: 'frontend-design',
      package: 'anthropics/skills@frontend-design',
      source: 'skills.sh',
      description: 'High-contrast accessible component design and responsive layout systems.',
      installed: profile.existingSkills.includes('frontend-design')
    });
    if (isTypeScript) {
      categories.PROJECT_SPECIFIC_SPECIALISTS.push({
        id: 'setup-ts-deep-modules',
        package: 'mattpocock/skills@setup-ts-deep-modules',
        source: 'mattpocock/skills',
        description: 'Enforce TypeScript deep module architectural boundaries via dependency-cruiser.',
        installed: profile.existingSkills.includes('setup-ts-deep-modules')
      });
    }
  } else if (profile.type.includes('Flutter')) {
    categories.CORE_PROJECT_SKILLS.push({
      id: 'flutter-specialist',
      package: 'flutter-community/skills@flutter-patterns',
      source: 'skills.sh',
      description: 'Flutter widget lifecycle, reactive state management (BLoC/Riverpod), and platform channels.',
      installed: profile.existingSkills.includes('flutter-specialist')
    });
    categories.PROJECT_SPECIFIC_SPECIALISTS.push({
      id: 'mobile-ui-ux',
      package: 'mobile-standards/skills@mobile-ui-ux',
      source: 'skills.sh',
      description: 'Mobile touch targets, haptics, safe-area insets, and iOS/Android human interface guidelines.',
      installed: profile.existingSkills.includes('mobile-ui-ux')
    });
  } else if (profile.type.includes('Laravel')) {
    categories.CORE_PROJECT_SKILLS.push({
      id: 'laravel-core',
      package: 'laravel-standards/skills@laravel-architecture',
      source: 'skills.sh',
      description: 'Laravel service providers, Eloquent relationships, form requests, and queue pipelines.',
      installed: profile.existingSkills.includes('laravel-core')
    });
    categories.QUALITY_ASSURANCE.push({
      id: 'pest-testing',
      package: 'pestphp/skills@pest-testing',
      source: 'skills.sh',
      description: 'Pest PHP elegant testing, dataset mutations, and architectural assertions.',
      installed: profile.existingSkills.includes('pest-testing')
    });
  } else if (isPython) {
    categories.CORE_PROJECT_SKILLS.push({
      id: 'codebase-design',
      package: 'mattpocock/skills@codebase-design',
      source: 'mattpocock/skills',
      description: 'Design deep modules with small interfaces and rich implementations.',
      installed: profile.existingSkills.includes('codebase-design')
    });
  } else {
    // Default software engineering core
    categories.CORE_PROJECT_SKILLS.push({
      id: 'codebase-design',
      package: 'mattpocock/skills@codebase-design',
      source: 'mattpocock/skills',
      description: 'Design deep modules with small interfaces and rich implementations.',
      installed: profile.existingSkills.includes('codebase-design')
    });
  }

  // Cross-cutting QA
  if (isTypeScript) {
    categories.PROJECT_SPECIFIC_SPECIALISTS.push({
      id: 'migrate-to-shoehorn',
      package: 'mattpocock/skills@migrate-to-shoehorn',
      source: 'mattpocock/skills',
      description: 'Safe test assertions migrating from "as" casts to @total-typescript/shoehorn.',
      installed: profile.existingSkills.includes('migrate-to-shoehorn')
    });
  }

  return categories;
}

/**
 * Execute installation of skills strictly in project scope.
 * Never uses global flags.
 */
function installProjectSkills(projectDir, skillsToInstall, options = {}) {
  const resolvedDir = path.resolve(projectDir);
  const results = {
    attempted: skillsToInstall.length,
    successful: [],
    skipped: [],
    failed: []
  };

  const agentsSkillDir = path.join(resolvedDir, '.agents', 'skills');
  if (!fs.existsSync(agentsSkillDir)) {
    fs.mkdirSync(agentsSkillDir, { recursive: true });
  }

  for (const skill of skillsToInstall) {
    const targetPath = path.join(agentsSkillDir, skill.id);
    
    // Safety check: preserve user-authored skills
    if (fs.existsSync(targetPath)) {
      results.skipped.push({
        id: skill.id,
        reason: 'Already installed on disk in project scope'
      });
      continue;
    }

    if (options.dryRun) {
      results.successful.push({
        id: skill.id,
        scope: 'project',
        dryRun: true,
        targetPath
      });
      continue;
    }

    try {
      // Execute project-scope installation using Skills CLI strictly without -g
      const cmd = `npx skills add ${skill.package || 'mattpocock/skills'} --skill ${skill.id} -y`;
      cp.execSync(cmd, {
        cwd: resolvedDir,
        stdio: 'pipe',
        env: { ...process.env, CI: 'true' }
      });

      if (fs.existsSync(targetPath)) {
        results.successful.push({
          id: skill.id,
          scope: 'project',
          targetPath,
          verified: true
        });
      }
    } catch (err) {
      results.failed.push({
        id: skill.id,
        error: err.message
      });
    }
  }

  return results;
}

const MO7I_OWN_SKILLS = new Set(['mo7i', 'mo7i-project-setup']);

/**
 * Verify post-install setup completion against concrete artifacts and file evidence.
 * Derived from real inspection in Phase 0B.
 */
function verifySetupCompletion(projectDir, setupSkillName) {
  const resolvedDir = path.resolve(projectDir);
  const sName = setupSkillName.toLowerCase().trim();

  // MO7I internal infrastructure skills require no companion post-install setup
  if (MO7I_OWN_SKILLS.has(sName)) {
    return {
      verified: true,
      evidence: 'MO7I internal infrastructure skill requires no companion post-install setup.',
      status: 'not-required'
    };
  }

  // 1. setup-matt-pocock-skills verification
  if (sName === 'setup-matt-pocock-skills') {
    const issueTracker = path.join(resolvedDir, 'docs', 'agents', 'issue-tracker.md');
    const domainDocs = path.join(resolvedDir, 'docs', 'agents', 'domain.md');
    const triageLabels = path.join(resolvedDir, 'docs', 'agents', 'triage-labels.md');
    const claudeMd = path.join(resolvedDir, 'CLAUDE.md');
    const agentsMd = path.join(resolvedDir, 'AGENTS.md');

    const trackerExists = fs.existsSync(issueTracker) && fs.statSync(issueTracker).size > 0;
    const domainExists = fs.existsSync(domainDocs) && fs.statSync(domainDocs).size > 0;
    const triageExists = fs.existsSync(triageLabels) && fs.statSync(triageLabels).size > 0;
    
    let instructionsHaveSection = false;
    if (fs.existsSync(claudeMd)) {
      const content = fs.readFileSync(claudeMd, 'utf8');
      if (content.includes('## Agent skills')) instructionsHaveSection = true;
    }
    if (!instructionsHaveSection && fs.existsSync(agentsMd)) {
      const content = fs.readFileSync(agentsMd, 'utf8');
      if (content.includes('## Agent skills')) instructionsHaveSection = true;
    }

    if (trackerExists && domainExists && triageExists && instructionsHaveSection) {
      return {
        verified: true,
        evidence: 'Verified: docs/agents/{issue-tracker,triage-labels,domain}.md and AGENTS.md/CLAUDE.md ## Agent skills section exist.',
        status: 'completed'
      };
    }
    return {
      verified: false,
      evidence: `Missing required artifacts (issue-tracker: ${trackerExists}, domain: ${domainExists}, triage-labels: ${triageExists}, instructionsSection: ${instructionsHaveSection})`,
      status: 'pending'
    };
  }

  // 2. setup-pre-commit verification
  if (sName === 'setup-pre-commit') {
    const preCommitHook = path.join(resolvedDir, '.husky', 'pre-commit');
    const lintStagedRc = path.join(resolvedDir, '.lintstagedrc');
    const pkgPath = path.join(resolvedDir, 'package.json');

    const hookExists = fs.existsSync(preCommitHook);
    const lintStagedExists = fs.existsSync(lintStagedRc);
    let pkgHasHusky = false;
    if (fs.existsSync(pkgPath)) {
      try {
        const pkg = JSON.parse(fs.readFileSync(pkgPath, 'utf8'));
        pkgHasHusky = pkg.scripts && typeof pkg.scripts.prepare === 'string' && pkg.scripts.prepare.includes('husky');
      } catch {
        // ignore
      }
    }

    if (hookExists && lintStagedExists && pkgHasHusky) {
      return {
        verified: true,
        evidence: 'Verified: .husky/pre-commit, .lintstagedrc, and package.json prepare:husky exist.',
        status: 'completed'
      };
    }
    return {
      verified: false,
      evidence: `Missing pre-commit artifacts (hook: ${hookExists}, lintStaged: ${lintStagedExists}, prepareScript: ${pkgHasHusky})`,
      status: 'pending'
    };
  }

  // 3. setup-ts-deep-modules verification
  if (sName === 'setup-ts-deep-modules') {
    const depCruiser = path.join(resolvedDir, '.dependency-cruiser.cjs');
    const pkgPath = path.join(resolvedDir, 'package.json');

    const configExists = fs.existsSync(depCruiser);
    let hasBoundaryScript = false;
    if (fs.existsSync(pkgPath)) {
      try {
        const pkg = JSON.parse(fs.readFileSync(pkgPath, 'utf8'));
        hasBoundaryScript = pkg.scripts && Boolean(pkg.scripts['lint:boundaries']);
      } catch {
        // ignore
      }
    }

    if (configExists && hasBoundaryScript) {
      return {
        verified: true,
        evidence: 'Verified: .dependency-cruiser.cjs and package.json lint:boundaries exist.',
        status: 'completed'
      };
    }
    return {
      verified: false,
      evidence: `Missing deep module artifacts (config: ${configExists}, script: ${hasBoundaryScript})`,
      status: 'pending'
    };
  }

  // 4. setup-pstack verification
  if (sName === 'setup-pstack') {
    const pstackGlobal = path.join(process.env.HOME || '/root', '.agents', 'pstack-models.md');
    const pstackLocal = path.join(resolvedDir, '.agents', 'pstack-models.md');
    const exists = fs.existsSync(pstackGlobal) || fs.existsSync(pstackLocal);
    if (exists) {
      return {
        verified: true,
        evidence: 'Verified: pstack-models.md configuration exists.',
        status: 'completed'
      };
    }
    return {
      verified: false,
      evidence: 'Missing pstack-models.md in ~/.agents/ or .agents/',
      status: 'pending'
    };
  }

  // Generic verification
  const mapData = loadPostInstallSetupMap();
  const entry = Object.values(mapData.collections).find(c => c.setupSkill === sName);
  if (entry && entry.verification && Array.isArray(entry.verification.files)) {
    const missing = entry.verification.files.filter(f => !fs.existsSync(path.join(resolvedDir, f)));
    if (missing.length === 0) {
      return {
        verified: true,
        evidence: `Verified all required files: ${entry.verification.files.join(', ')}`,
        status: 'completed'
      };
    }
  }

  return {
    verified: false,
    evidence: 'Setup configuration not verified on disk.',
    status: 'pending'
  };
}

/**
 * Detects post-install setup companion skills across installed skills.
 * Dynamic inspection of SKILL.md takes precedence over curated map.
 */
function detectPostInstallSetups(projectDir, installedSkills = [], options = {}) {
  const resolvedDir = path.resolve(projectDir);
  const mapData = loadPostInstallSetupMap();
  const detected = new Map();

  // Read existing registry to preserve skipped or completed state
  const regPath = path.join(resolvedDir, '.mo7i', 'skill-registry.json');
  let existingRegistry = null;
  if (fs.existsSync(regPath)) {
    try {
      existingRegistry = JSON.parse(fs.readFileSync(regPath, 'utf8'));
    } catch {
      // ignore
    }
  }

  const existingStatusMap = new Map();
  if (existingRegistry && Array.isArray(existingRegistry.installedSkills)) {
    for (const s of existingRegistry.installedSkills) {
      if (s.setupSkill) {
        existingStatusMap.set(s.setupSkill, s);
      }
    }
  }

  const agentsSkillDir = path.join(resolvedDir, '.agents', 'skills');
  const allSkillDirs = [];
  if (fs.existsSync(agentsSkillDir)) {
    try {
      const dirs = fs.readdirSync(agentsSkillDir);
      for (const d of dirs) {
        if (fs.existsSync(path.join(agentsSkillDir, d, 'SKILL.md'))) {
          allSkillDirs.push(d);
        }
      }
    } catch {
      // ignore
    }
  }

  // Combine with passed skills, strictly excluding MO7I's own internal skills
  const combinedSkills = Array.from(new Set([...allSkillDirs, ...installedSkills]))
    .filter(skillId => !MO7I_OWN_SKILLS.has(skillId.toLowerCase().trim()));

  // 1. Dynamic inspection of installed SKILL.md files
  for (const skillId of combinedSkills) {
    if (MO7I_OWN_SKILLS.has(skillId.toLowerCase().trim())) {
      continue;
    }

    const skillMdPath = path.join(agentsSkillDir, skillId, 'SKILL.md');
    let isSetupSkill = false;
    let description = '';
    let interactive = false;
    if (fs.existsSync(skillMdPath)) {
      try {
        const content = fs.readFileSync(skillMdPath, 'utf8');
        const descMatch = /description:\s*["']?([^"'\n\r]+)/i.exec(content);
        if (descMatch) description = descMatch[1].trim();

        // 1. Match setup naming convention strictly: setup-*, init-*, onboard*
        if (/^(setup|init|onboard)[-_]/i.test(skillId) || /[-_](setup|init|onboard)$/i.test(skillId)) {
          isSetupSkill = true;
        }

        // 2. OR match description explicitly declaring repository initialization:
        // "run once", "before first use", "configure this repo"
        if (/run once\b|before first use\b|configure this repo\b/i.test(description)) {
          isSetupSkill = true;
        }

        // Match interactivity patterns
        if (/(ask the user|confirm with the user|prompt-driven|questions?|interview|choices?)/i.test(content)) {
          interactive = true;
        }
      } catch {
        // ignore
      }
    } else if (mapData.collections[skillId] || Object.values(mapData.collections).some(c => c.setupSkill === skillId)) {
      isSetupSkill = true;
    }

    if (isSetupSkill) {
      const existing = existingStatusMap.get(skillId);
      const verification = verifySetupCompletion(resolvedDir, skillId);

      let status = 'pending';
      let evidence = verification.evidence;
      let skipReason = null;

      if (verification.verified) {
        status = 'completed';
      } else if (existing && existing.setupStatus === 'skipped') {
        status = 'skipped';
        skipReason = existing.skipReason;
      } else if (existing && existing.setupStatus === 'failed') {
        status = 'failed';
      }

      detected.set(skillId, {
        setupSkill: skillId,
        interactive,
        description: description || 'Companion post-install repository setup skill',
        status,
        evidence: verification.verified ? verification.evidence : (existing ? existing.evidence : evidence),
        completedAt: verification.verified ? (existing?.completedAt || new Date().toISOString()) : null,
        skipReason
      });
    }
  }

  // 2. Collection mapping fallback
  for (const [collKey, collInfo] of Object.entries(mapData.collections)) {
    const setupSkill = collInfo.setupSkill;
    if (MO7I_OWN_SKILLS.has(setupSkill.toLowerCase().trim())) {
      continue;
    }
    if (combinedSkills.includes(setupSkill) && !detected.has(setupSkill)) {
      const verification = verifySetupCompletion(resolvedDir, setupSkill);
      const existing = existingStatusMap.get(setupSkill);

      let status = 'pending';
      if (verification.verified) {
        status = 'completed';
      } else if (existing && existing.setupStatus === 'skipped') {
        status = 'skipped';
      }

      detected.set(setupSkill, {
        setupSkill,
        interactive: collInfo.interactive,
        description: collInfo.description,
        status,
        evidence: verification.evidence,
        completedAt: verification.verified ? new Date().toISOString() : null,
        skipReason: existing?.skipReason || null
      });
    }
  }

  return Array.from(detected.values());
}

/**
 * Mark a pending post-install setup as skipped.
 */
function skipSetup(projectDir, setupSkillName, reason = 'Skipped by user') {
  const resolvedDir = path.resolve(projectDir);
  const regPath = path.join(resolvedDir, '.mo7i', 'skill-registry.json');
  if (!fs.existsSync(regPath)) {
    throw new Error(`Registry not found at ${regPath}. Run setup first.`);
  }

  const registry = JSON.parse(fs.readFileSync(regPath, 'utf8'));
  let matched = false;

  for (const skill of registry.installedSkills) {
    if (skill.id === setupSkillName || skill.setupSkill === setupSkillName) {
      skill.setupStatus = 'skipped';
      skill.skipReason = reason;
      skill.updatedAt = new Date().toISOString();
      matched = true;
    }
  }

  if (Array.isArray(registry.pendingSetups)) {
    registry.pendingSetups = registry.pendingSetups.filter(s => s.setupSkill !== setupSkillName);
  }

  registry.updatedAt = new Date().toISOString();
  fs.writeFileSync(regPath, JSON.stringify(registry, null, 2));

  return {
    success: true,
    setupSkill: setupSkillName,
    status: 'skipped',
    reason
  };
}

/**
 * Format Human Gate notification for pending interactive post-install setups.
 */
function formatHumanGateNotice(pendingSetups) {
  if (!pendingSetups || pendingSetups.length === 0) return '';

  let out = `\n======================================================================\n`;
  out += `[MO7I HUMAN GATE: POST-INSTALL SETUP REQUIRED]\n`;
  out += `The following installed skill(s) require interactive repository configuration:\n\n`;

  for (const s of pendingSetups) {
    out += `• ${s.setupSkill}:\n`;
    out += `  Description: ${s.description}\n`;
    out += `  Invocation:  $${s.setupSkill} (in agent sessions) or /${s.setupSkill}\n`;
    out += `  Interactive: ${s.interactive ? 'YES (Human decisions required - MO7I will NOT invent answers)' : 'NO'}\n`;
    out += `  To Skip:     /setup-project-skills skip-setup ${s.setupSkill} --reason "<reason>"\n\n`;
  }

  out += `ACTION: Please invoke the setup command in your session to configure this repo.\n`;
  out += `======================================================================\n`;
  return out;
}

/**
 * Reconcile skills on disk with skills in .mo7i/skill-registry.json.
 * Fails / flags if skills exist on disk in .agents/skills/ but not in the registry.
 */
function reconcileSkills(projectDir = process.cwd()) {
  const resolvedDir = path.resolve(projectDir);
  const agentsSkillDir = path.join(resolvedDir, '.agents', 'skills');
  const regPath = path.join(resolvedDir, '.mo7i', 'skill-registry.json');

  const diskSkills = [];
  if (fs.existsSync(agentsSkillDir)) {
    try {
      const entries = fs.readdirSync(agentsSkillDir, { withFileTypes: true });
      for (const e of entries) {
        if (e.isDirectory()) {
          const sMd = path.join(agentsSkillDir, e.name, 'SKILL.md');
          if (fs.existsSync(sMd)) diskSkills.push(e.name);
        }
      }
    } catch {
      // ignore
    }
  }

  let registeredSkills = [];
  let registryData = null;
  if (fs.existsSync(regPath)) {
    try {
      registryData = JSON.parse(fs.readFileSync(regPath, 'utf8'));
      registeredSkills = (registryData.installedSkills || []).map(s => s.id);
    } catch {
      // ignore
    }
  }

  const unregisteredOnDisk = diskSkills.filter(id => !registeredSkills.includes(id));
  const missingFromDisk = registeredSkills.filter(id => !diskSkills.includes(id));

  return {
    reconciled: unregisteredOnDisk.length === 0,
    diskSkills,
    registeredSkills,
    unregisteredOnDisk,
    missingFromDisk,
    registryExists: Boolean(registryData)
  };
}

/**
 * Writes or updates the project registry files (.mo7i/*) with backups.
 */
function saveProjectRegistry(projectDir, profile, recommendations, installResults, mode = 'initial') {
  const resolvedDir = path.resolve(projectDir);
  const mo7iDir = path.join(resolvedDir, '.mo7i');
  if (!fs.existsSync(mo7iDir)) {
    fs.mkdirSync(mo7iDir, { recursive: true });
  }

  const profilePath = path.join(mo7iDir, 'project-profile.json');
  const profileBak = path.join(mo7iDir, 'project-profile.json.bak');
  const registryPath = path.join(mo7iDir, 'skill-registry.json');
  const registryBak = path.join(mo7iDir, 'skill-registry.json.bak');
  const reportPath = path.join(mo7iDir, 'setup-report.md');

  // Preserve backup if existing files exist
  if (fs.existsSync(profilePath)) fs.copyFileSync(profilePath, profileBak);
  if (fs.existsSync(registryPath)) fs.copyFileSync(registryPath, registryBak);

  // Update profile
  profile.setupStatus = mode === 'audit' ? 'AUDITED' : mode === 'repair' ? 'REPAIRED' : mode === 'update' ? 'UPDATED' : 'INITIALIZED';
  profile.lastAuditAt = new Date().toISOString();
  profile.setupVersion = ENGINE_VERSION;

  // Reconcile and detect post-install setups
  const postInstallSetups = detectPostInstallSetups(resolvedDir, profile.existingSkills);
  const setupBySkillId = new Map(postInstallSetups.map(s => [s.setupSkill, s]));

  // Build registry data discovering ALL skills on disk
  const allDiscoveredSkillIds = Array.from(new Set([
    ...profile.existingSkills,
    ...(installResults ? installResults.successful.map(x => x.id) : [])
  ]));

  const allRecommended = [
    ...recommendations.CORE_PROJECT_SKILLS,
    ...recommendations.DEVELOPMENT_SKILLS,
    ...recommendations.QUALITY_ASSURANCE,
    ...recommendations.PROJECT_SPECIFIC_SPECIALISTS,
    ...recommendations.OPTIONAL_SKILLS
  ];
  const recMap = new Map(allRecommended.map(r => [r.id, r]));

  const installedList = [];
  for (const sId of allDiscoveredSkillIds) {
    const rec = recMap.get(sId);
    const setupInfo = setupBySkillId.get(sId);

    // Determine companion setup skill
    let setupSkill = null;
    let setupStatus = 'not-required';
    let completedAt = null;
    let evidence = null;
    let skipReason = null;

    if (setupInfo) {
      setupSkill = setupInfo.setupSkill;
      setupStatus = setupInfo.status;
      completedAt = setupInfo.completedAt;
      evidence = setupInfo.evidence;
      skipReason = setupInfo.skipReason;
    } else if (rec?.package === 'mattpocock/skills' && setupBySkillId.has('setup-matt-pocock-skills')) {
      const pocockSetup = setupBySkillId.get('setup-matt-pocock-skills');
      setupSkill = 'setup-matt-pocock-skills';
      setupStatus = pocockSetup.status;
      completedAt = pocockSetup.completedAt;
      evidence = pocockSetup.evidence;
      skipReason = pocockSetup.skipReason;
    }

    installedList.push({
      id: sId,
      package: rec?.package || 'project/custom',
      source: rec?.source || 'local',
      category: rec?.category || 'PROJECT_SPECIALIST',
      scope: 'project',
      installedAt: new Date().toISOString(),
      setupStatus,
      setupSkill,
      completedAt,
      evidence,
      skipReason
    });
  }

  const missingList = allRecommended
    .filter(s => !installedList.some(x => x.id === s.id))
    .map(s => ({ id: s.id, category: s.category, package: s.package }));

  const pendingSetups = postInstallSetups.filter(s => s.status === 'pending');

  const registryData = {
    version: ENGINE_VERSION,
    projectId: profile.id,
    projectPath: resolvedDir,
    scope: 'project',
    updatedAt: new Date().toISOString(),
    mattPocockCollection: {
      officialSource: MATT_POCOCK_OFFICIAL_SOURCE.repository,
      package: MATT_POCOCK_OFFICIAL_SOURCE.packageId,
      totalDiscovered: MATT_POCOCK_OFFICIAL_SOURCE.totalDiscovered,
      verifiedCount: MATT_POCOCK_OFFICIAL_SOURCE.validCount,
      skippedCount: MATT_POCOCK_OFFICIAL_SOURCE.skippedCount
    },
    installedSkills: installedList,
    missingRecommendedSkills: missingList,
    pendingSetups: pendingSetups.map(p => ({
      setupSkill: p.setupSkill,
      interactive: p.interactive,
      description: p.description,
      status: p.status
    })),
    postInstallSetupsSummary: {
      total: postInstallSetups.length,
      pending: pendingSetups.length,
      completed: postInstallSetups.filter(s => s.status === 'completed').length,
      skipped: postInstallSetups.filter(s => s.status === 'skipped').length,
      failed: postInstallSetups.filter(s => s.status === 'failed').length
    }
  };

  fs.writeFileSync(profilePath, JSON.stringify(profile, null, 2));
  fs.writeFileSync(registryPath, JSON.stringify(registryData, null, 2));

  // Generate and write setup report
  const reportMd = generateSetupReport(profile, registryData, installResults, mode);
  fs.writeFileSync(reportPath, reportMd);

  return {
    profilePath,
    registryPath,
    reportPath,
    registryData,
    pendingSetups,
    humanGateNotice: formatHumanGateNotice(pendingSetups)
  };
}

/**
 * Generate Markdown setup report.
 */
function generateSetupReport(profile, registryData, installResults, mode = 'initial') {
  const pendingSetups = registryData.pendingSetups || [];
  return `# MO7I Project Intelligence & Skill Setup Report (v${ENGINE_VERSION})

- **Timestamp**: \`${new Date().toISOString()}\`
- **Execution Mode**: \`${mode.toUpperCase()}\`
- **Project Workspace**: \`${profile.path}\`
- **Project Archetype**: **${profile.type}**
- **Setup Status**: \`${profile.setupStatus}\`

---

## 1. Technical Stack Analysis

- **Languages**: ${profile.languages.join(', ') || 'N/A'}
- **Frameworks**: ${profile.frameworks.map(f => `${f.name} (${f.version})`).join(', ') || 'None'}
- **Architecture**: ${profile.architecture}
- **Databases & Storage**: ${profile.databases.join(', ') || 'None'}
- **Testing Suites**: ${profile.testFrameworks.join(', ') || 'Standard / None'}
- **Code Quality & Linting**: ${profile.codeQualityConventions.join(', ') || 'Standard'}
- **Deployment Archetype**: ${profile.deployment}
- **Existing Development Tools**: ${profile.existingTools.join(', ') || 'Standard'}

---

## 2. Matt Pocock Official Skills Collection

- **Publisher**: \`${MATT_POCOCK_OFFICIAL_SOURCE.publisher}\`
- **Repository**: \`${MATT_POCOCK_OFFICIAL_SOURCE.repository}\`
- **Catalog Registry**: \`${MATT_POCOCK_OFFICIAL_SOURCE.skillsShUrl}\`
- **Total Discovered Skills**: \`${MATT_POCOCK_OFFICIAL_SOURCE.totalDiscovered}\`
- **Verified & Safe for Project Scope**: \`${MATT_POCOCK_OFFICIAL_SOURCE.validCount}\`
- **Skipped Upstream**: \`${MATT_POCOCK_OFFICIAL_SOURCE.skippedCount}\` (\`software-factory\`: ${MATT_POCOCK_OFFICIAL_SOURCE.skippedDetails[0].reason})

---

## 3. Post-Install Setup Management

${pendingSetups.length > 0 ? `> [!IMPORTANT]
> **Pending Post-Install Setup Steps Detected**:
${pendingSetups.map(p => `> - **\`$${p.setupSkill}\`** (Interactive: ${p.interactive ? 'YES' : 'NO'}): ${p.description}`).join('\n')}
> Run the setup skills in your session to complete configuration.` : '_All installed skills are fully configured or have no post-install setup requirements._'}

---

## 4. Skill Inventory & Categorization

| Skill ID | Category | Scope | Setup Status | Evidence |
| :--- | :--- | :--- | :--- | :--- |
${registryData.installedSkills.map(s => `| \`${s.id}\` | ${s.category} | ${s.scope} | \`${s.setupStatus || 'not-required'}\` | ${s.evidence || 'N/A'} |`).join('\n') || '| None | - | - | - | - |'}

---

## 5. Missing Recommended Skills

${registryData.missingRecommendedSkills.length > 0
  ? registryData.missingRecommendedSkills.map(s => `- **${s.id}** (${s.category}) from \`${s.package}\``).join('\n')
  : '_All recommended foundational specialist skills are active in project scope._'}

---

## 6. Security & Isolation Compliance

- **Scope Isolation**: Strictly project-scoped (\`./.agents/skills/\`). Global (\`-g\`) installation prohibited.
- **Credential Protection**: Zero credentials or secrets persisted.
- **Human Gate**: Interactive post-install questions require explicit user execution; never auto-answered silently.
- **User Skill Preservation**: Existing local skills and custom scripts preserved intact.
`;
}

/**
 * Main execution dispatcher supporting all operating modes.
 */
function runEngine(mode = 'initial', projectDir = process.cwd(), options = {}) {
  const resolvedDir = path.resolve(projectDir);
  const profile = analyzeProject(resolvedDir);
  const recommendations = recommendProjectSkills(profile);
  const postInstallSetups = detectPostInstallSetups(resolvedDir, profile.existingSkills, options);
  const reconciliation = reconcileSkills(resolvedDir);

  if (mode === 'audit') {
    const pendingSetups = postInstallSetups.filter(s => s.status === 'pending');
    const missingSkills = [
      ...recommendations.CORE_PROJECT_SKILLS,
      ...recommendations.DEVELOPMENT_SKILLS,
      ...recommendations.QUALITY_ASSURANCE
    ].filter(s => !profile.existingSkills.includes(s.id));
    const gapReport = missingSkills.length === 0 ? 'no gap' : `${missingSkills.length} gap(s) detected`;
    const gapAnalysis = {
      status: missingSkills.length === 0 ? 'NO_GAP' : 'GAP_DETECTED',
      report: gapReport,
      message: missingSkills.length === 0 ? 'no gap (existing tools suffice for the current task)' : `${missingSkills.length} missing skill(s) detected`,
      missingSkills
    };
    return {
      mode: 'audit',
      profile,
      recommendations,
      existingSkills: profile.existingSkills,
      missingSkills,
      gapAnalysis,
      gapReport,
      postInstallSetups,
      pendingSetups,
      reconciliation
    };
  }

  if (mode === 'skip-setup') {
    const setupSkill = options.setupSkill || options.skill;
    if (!setupSkill) throw new Error('Setup skill ID required for skip-setup');
    return skipSetup(resolvedDir, setupSkill, options.reason || 'Skipped by user');
  }

  if (mode === 'verify-setup') {
    const setupSkill = options.setupSkill || options.skill;
    if (!setupSkill) throw new Error('Setup skill ID required for verify-setup');
    const ver = verifySetupCompletion(resolvedDir, setupSkill);
    if (ver.verified) {
      saveProjectRegistry(resolvedDir, profile, recommendations, null, 'audit');
    }
    return ver;
  }

  if (mode === 'repair') {
    const mo7iRegistryPath = path.join(resolvedDir, '.mo7i', 'skill-registry.json');
    let targetSkills = [];
    if (fs.existsSync(mo7iRegistryPath)) {
      try {
        const reg = JSON.parse(fs.readFileSync(mo7iRegistryPath, 'utf8'));
        const recorded = reg.installedSkills || [];
        for (const s of recorded) {
          const sPath = path.join(resolvedDir, '.agents', 'skills', s.id);
          if (!fs.existsSync(sPath)) {
            targetSkills.push(s);
          }
        }
      } catch {
        targetSkills = recommendations.CORE_PROJECT_SKILLS;
      }
    } else {
      targetSkills = recommendations.CORE_PROJECT_SKILLS;
    }

    const installResults = installProjectSkills(resolvedDir, targetSkills, options);
    const registryInfo = saveProjectRegistry(resolvedDir, profile, recommendations, installResults, 'repair');
    return { mode: 'repair', profile, installResults, registryInfo, reconciliation };
  }

  if (mode === 'update') {
    const missingCore = recommendations.CORE_PROJECT_SKILLS.filter(s => !profile.existingSkills.includes(s.id));
    const missingDev = recommendations.DEVELOPMENT_SKILLS.filter(s => !profile.existingSkills.includes(s.id));
    const toInstall = [...missingCore, ...missingDev];

    const installResults = installProjectSkills(resolvedDir, toInstall, options);
    const registryInfo = saveProjectRegistry(resolvedDir, profile, recommendations, installResults, 'update');
    return { mode: 'update', profile, installResults, registryInfo, reconciliation };
  }

  if (mode === 'report') {
    const mo7iRegistryPath = path.join(resolvedDir, '.mo7i', 'skill-registry.json');
    let regData = { installedSkills: [], missingRecommendedSkills: [], pendingSetups: [] };
    if (fs.existsSync(mo7iRegistryPath)) {
      try {
        regData = JSON.parse(fs.readFileSync(mo7iRegistryPath, 'utf8'));
      } catch {
        // ignore
      }
    }
    const reportMd = generateSetupReport(profile, regData, null, 'report');
    return { mode: 'report', reportMd };
  }

  // Default: mode === 'initial'
  const rawSkillsToInstall = [
    ...recommendations.CORE_PROJECT_SKILLS,
    ...recommendations.MATT_POCOCK_COLLECTION
  ];
  const seenSkillIds = new Set();
  const skillsToInstall = [];
  for (const s of rawSkillsToInstall) {
    if (!seenSkillIds.has(s.id)) {
      seenSkillIds.add(s.id);
      skillsToInstall.push(s);
    }
  }

  const installResults = installProjectSkills(resolvedDir, skillsToInstall, options);
  const registryInfo = saveProjectRegistry(resolvedDir, profile, recommendations, installResults, 'initial');

  return {
    mode: 'initial',
    profile,
    recommendations,
    installResults,
    registryInfo,
    reconciliation
  };
}

module.exports = {
  ENGINE_VERSION,
  MATT_POCOCK_OFFICIAL_SOURCE,
  MO7I_OWN_SKILLS: Array.from(MO7I_OWN_SKILLS),
  analyzeProject,
  recommendProjectSkills,
  installProjectSkills,
  verifySetupCompletion,
  detectPostInstallSetups,
  skipSetup,
  formatHumanGateNotice,
  reconcileSkills,
  findAvailablePort,
  findAvailablePortSync,
  saveProjectRegistry,
  generateSetupReport,
  scanContentSecurity,
  sanitizeObject,
  runEngine
};

// Direct CLI invocation
if (require.main === module) {
  const args = process.argv.slice(2);
  const mode = args[0] || 'initial';
  const targetDir = args[1] && !args[1].startsWith('--') ? args[1] : process.cwd();
  const dryRun = args.includes('--dry-run');

  try {
    const result = runEngine(mode, targetDir, { dryRun });
    if (mode === 'report') {
      console.log(result.reportMd);
    } else {
      console.log(`MO7I Project Intelligence Engine (${mode}) completed successfully.`);
      console.log(`Project Archetype: ${result.profile ? result.profile.type : 'N/A'}`);
      if (result.registryInfo) {
        console.log(`Registry written to: ${result.registryInfo.registryPath}`);
        console.log(`Setup report written to: ${result.registryInfo.reportPath}`);
        if (result.registryInfo.humanGateNotice) {
          console.log(result.registryInfo.humanGateNotice);
        }
      }
    }
  } catch (err) {
    console.error(`MO7I Setup Engine Error: ${err.message}`);
    process.exit(1);
  }
}
