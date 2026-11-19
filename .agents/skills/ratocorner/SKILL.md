---
name: ratocorner
description: Guidelines and domain rules for Ratocorner Coffee Shop ERP application including POS, inventory, recipe costing (HPP), tables QR ordering, and audit logging.
---

# Ratocorner Coffee Shop ERP Skill

Ratocorner is an ERP & POS system specifically tailored for coffee shops, cafes, and eateries.

## User Roles & Permissions
- **Owner**: Full access to financial reports, cafe settings, master data, user management, and deletion audit logs.
- **Manager**: Access to inventory, stock opname, recipes & HPP, table management, customer CRM, and POS operations.
- **Kasir (Cashier)**: Access to POS cashier screen, order entry, QR order processing, and payment settlement. Cannot delete orders without manager/owner approval or logging.

## Core Domain Modules

1. **Cafe Settings (`cafe_settings`)**:
   - Master configuration: shop name ("Ratocorner"), tagline, address, phone, tax percentage (PPN), service charge, and receipt layouts.

2. **QR Table Management (`tables`)**:
   - Table numbering and capacity.
   - Unique QR token generation for customer self-ordering via QR code.
   - Table status tracking (`available`, `occupied`, `reserved`).

3. **Vendor & Supply Chain (`vendors`)**:
   - Supplier directories for coffee beans, dairy, syrups, packaging, and raw goods.

4. **Ingredients & Inventory (`ingredients`)**:
   - Raw material items with tracking units (gram, ml, pcs).
   - Real-time stock balance, minimum stock alerts, and unit cost price for HPP calculation.

5. **Products & Categories (`categories`, `products`)**:
   - Menu items sold to customers.
   - Pricing, selling status, and HPP (Cost of Goods Sold).

6. **Recipes & HPP (`product_recipes`)**:
   - Bill of Materials (BOM) linking products with raw ingredients.
   - Calculation: `HPP = Sum(ingredient_quantity * ingredient_cost_per_unit)`.

7. **Customers & Loyalty (`customers`)**:
   - Customer profile, order history, and loyalty points.

8. **Orders & Transactions (`orders`, `order_items`)**:
   - Cashier POS & dine-in QR orders.
   - Soft deletes (`SoftDeletes`) enabled to preserve financial history.
   - Order types: `dine_in`, `take_away`, `delivery`.
   - Payment status & methods (Cash, QRIS, Debit, Credit, Transfer).

9. **Order Deletion Audit Logs (`order_deletion_logs`)**:
   - Prevents cashier fraud: any canceled/deleted order must be logged with user id, timestamp, reason, and full snapshot of the order data.

10. **Stock Opname (`stock_opnames`)**:
    - Physical inventory audits comparing system stock vs physical stock.
    - Tracks shrinkage, wastage, and cost variances.
