$env:GIT_PAGER = ""
git add -A
git commit -m "Fix RoleSeeder unique constraint, configure dynamic Nginx PORT for Render, and seed rich B2B catalog"
git push origin main
