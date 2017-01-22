# Notification Webhooks

Webhooks allow you to build integrations which react to certain events from Deployer.
When one of these events is triggered, a HTTP POST request will be made to the configured URL.

Webhooks can be configured from the "Notifications" tab.

## Events

When configuring the webhook you can choose which events will trigger a request. The request will contain a
`X-Deployer-Event` header with a value of the event which triggered it.

| Event                | Description                                            |
|----------------------|--------------------------------------------------------|
| notification_test    | Triggered when adding or editing a notification        |
| deployment_succeeded | Triggered when a deployment finishes successfully      |
| deployment_failed    | Triggered when a deployment fails                      |
| heartbeat_missing    | Triggered when a heartbeat does not check-in on time   |
| heartbeat_recovered  | Triggered when a previously missing heartbeat recovers |

## Payloads

Each event has specific payload content, the payload is sent as JSON.

### Headers

Each request will contain the following headers.

| Header                     | Description                                                            |
|----------------------------|------------------------------------------------------------------------|
| X-Deployer-Event           | The event which triggered the webhook                                  |
| X-Deployer-Project-Id      | The unique ID for the project the notification belongs to              |
| X-Deployer-Notification-Id | The unique ID for the notification channel which triggered the webhook |
| User-Agent                 | Deployer plus the version, appended to the Guzzle User-Agent string    |
| Content-Type               | Will be set to application/json                                        |
