# Workflow Update sample

This sample demonstrates the Workflow Update feature using a turn of the game Zonk (Farkle) as an example.
The state of the virtual table with dice is stored in the Workflow.
All player actions are performed through Update functions with pre-validation.
Invalid user actions will be rejected at the validation stage.

From the root of the project, run the following command:

```bash
php ./app/app.php update
```
