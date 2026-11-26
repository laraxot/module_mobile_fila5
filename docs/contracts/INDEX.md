# Mobile behavioral contract index

This is the machine-readable inventory of behavioral contracts owned by the **Mobile** module.

A contract describes an existing public operation or boundary. It names the owner, input, output, stable invariants, side effects and their order, errors, and non-goals. Every invariant gets a stable ID and a Pest group when a test exists; missing coverage is recorded as debt instead of being implied away.

## Contracts
- [kds-notification-action](kds-notification-action.md)
- [scan-qr-action](scan-qr-action.md)
- [split-bill-action](split-bill-action.md)
- [sync-offline-orders-action](sync-offline-orders-action.md)
- [take-order-action](take-order-action.md)

## Authoring

Use [Xot contract authoring standard](../../../Xot/docs/contracts/contract-authoring-standard.md) and the shared template. Legacy behavior is documented as-is and is not a model for new code.
