# User Flow - Manager

```mermaid
graph LR
    Start((Start)) --> Login[Login]
    Login --> Dash[Manager Dashboard]
    Dash --> Stats[Analyze Revenue/Top Products]
    Stats --> Export[Export CSV Report]
    Export --> Done((Report Generated))
```
