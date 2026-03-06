#!/usr/bin/env bash

set -u

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
HANDOFF_FILE="$REPO_ROOT/HANDOFF.md"

echo "Startup checklist:"
echo "1. git pull --rebase"

if [ -d "$REPO_ROOT/vendor" ]; then
  echo "2. composer install (optional: vendor/ already present)"
else
  echo "2. composer install (required: vendor/ missing)"
fi

if [ -d "$REPO_ROOT/node_modules" ]; then
  echo "3. npm install (optional: node_modules/ already present)"
else
  echo "3. npm install (required: node_modules/ missing)"
fi

echo "4. php artisan migrate"
echo "5. php artisan optimize:clear"
echo "6. Start servers in separate terminals:"
echo "   - php artisan serve"
echo "   - npm run dev"
echo ""

if ! command -v git >/dev/null 2>&1; then
  echo "Git not available in PATH."
else
  if git -C "$REPO_ROOT" rev-parse --is-inside-work-tree >/dev/null 2>&1; then
    branch="$(git -C "$REPO_ROOT" branch --show-current 2>/dev/null)"
    [ -n "$branch" ] || branch="detached HEAD"
    echo "Current branch: $branch"
    echo "Working tree status:"
    git -C "$REPO_ROOT" status --short
    echo ""
  else
    echo "Not in a Git repository: $REPO_ROOT"
  fi
fi

if [ ! -f "$HANDOFF_FILE" ]; then
  echo "HANDOFF.md not found at: $HANDOFF_FILE"
  exit 0
fi

echo "Next steps from HANDOFF.md:"
next_steps="$(awk '
  $0 == "## Next steps" {in_section=1; print; next}
  in_section && /^## / {exit}
  in_section {print}
' "$HANDOFF_FILE")"

if [ -n "$next_steps" ]; then
  printf "%s\n" "$next_steps"
else
  echo "## Next steps"
  echo "- [ ] Add next steps in HANDOFF.md"
fi
