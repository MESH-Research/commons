---
name: WordPress Update
about: WordPress update and testing process
title: WordPress Update - [version]
labels: ''
assignees: ''

---

- [ ] Developer: [Generate update script](https://github.com/MESH-Research/hc-admin-docs-support/blob/main/documentation/developer/how-tos/updating-wordpress.md#updating-wordpress-1)
- [ ] Developer: Update development server for testing
- [ ] Developer: Ensure WordPress loads, has basic functionality
- [ ] Testing: Run full test protocol
    - Any potential problems should be checked against production and then be registered as separate issues within the update epic.
- [ ] Developer: All update issues have been addressed
- [ ] Developer: Update script has been revised based on testing
- [ ] Developer: Run updates on production
- [ ] Testing: Run abbreviated test protocol to ensure core functionality
