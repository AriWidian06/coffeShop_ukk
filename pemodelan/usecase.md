# Use Case Diagrams

## Customer
```mermaid
useCaseDiagram
    actor "Customer" as Customer
    
    package "Order System" {
        usecase "Select Table & Menu" as UC1
        usecase "Place Order" as UC2
    }

    Customer --> UC1
    Customer --> UC2
```

## Kasir
```mermaid
useCaseDiagram
    actor "Kasir" as Kasir
    
    package "Order System" {
        usecase "Manage Active Orders" as UC3
        usecase "Process Payment" as UC4
        usecase "Manage Table Layout" as UC5
    }

    Kasir --> UC3
    Kasir --> UC4
    Kasir --> UC5
```

## Manager
```mermaid
useCaseDiagram
    actor "Manager" as Manager
    
    package "Order System" {
        usecase "View Performance Dashboard" as UC5
        usecase "Export Sales Report" as UC6
    }

    Manager --> UC5
    Manager --> UC6
```

## Admin
```mermaid
useCaseDiagram
    actor "Admin" as Admin
    
    package "Order System" {
        usecase "Manage Users/Employees" as UC7
        usecase "Manage Catalog/Products" as UC8
        usecase "Manage Table Layout" as UC9
        usecase "Manage Suppliers" as UC10
        usecase "View Dashboard" as UC11
    }

    Admin --> UC7
    Admin --> UC8
    Admin --> UC9
    Admin --> UC10
    Admin --> UC11
```
