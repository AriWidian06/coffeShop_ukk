# User Flow - Customer

```mermaid
graph LR
    Start((Start)) --> Welcome[Welcome Page]
    Welcome --> SelectTable[Select Table]
    SelectTable --> SelectMenu[Select Products]
    SelectMenu --> SubmitOrder[Submit Order]
    SubmitOrder --> Done((Order Placed))
```
