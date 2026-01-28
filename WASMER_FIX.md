# Fix: Wasmer Deployment Error

## The Problem

Wasmer deployment failed with:
```
Autobuild failed with error: Error at $: 'kind' is a required property
```

## The Solution

The `app.yaml` file was missing the required `kind` property. I've updated it to include:

```yaml
kind: wasmer.io/App.v1
```

This tells Wasmer Edge what type of configuration file this is.

## Updated app.yaml

The file now includes the `kind` field at the top:

```yaml
kind: wasmer.io/App.v1
name: todo
description: Simple PHP Todo Application
# ... rest of config
```

## Next Steps

1. **Commit the fix:**
   ```bash
   git add app.yaml
   git commit -m "Add kind property to app.yaml for Wasmer"
   git push
   ```

2. **Redeploy on Wasmer:**
   - Go to Wasmer Edge dashboard
   - Trigger a new deployment
   - The deployment should now succeed!

## What Changed

- ✅ Added `kind: wasmer.io/App.v1` to `app.yaml`
- This is a required field for Wasmer Edge to recognize the configuration

## Verification

After deploying, check:
1. Build completes successfully ✅
2. App starts without errors ✅
3. Database is provisioned ✅
4. App is accessible at your Wasmer URL ✅
