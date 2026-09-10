# IVC

International Vacation Club PHP site: public landing, member bookings, partner directory, and admin console.

## Production deploy

GitHub repo: `https://github.com/lukalinks/IVC.git` (branch `main`).

Production must use this remote. If `git pull` does nothing, the server may still point at the old `sustecdev/IVC` remote.

In **cPanel → Terminal** or SSH:

```bash
cd /home/internationalvacationclub/public_html
git remote set-url origin https://github.com/lukalinks/IVC.git
git fetch origin main
git reset --hard origin/main
```

Or run `bash scripts/deploy-production.sh`.

After deploy:

1. Ensure `/home/db-config2.php` (or `ivc/db-config2.php`) has correct MySQL credentials for `ivc_prod`.
2. Hard-refresh the browser (Ctrl+F5).
3. Confirm login page shows **Forgot Account #?** links (means the update applied).
