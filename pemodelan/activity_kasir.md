# Activity Diagram - Kasir

## Order Processing
```mermaid
activityDiagram
    start
    :Receive Order Notification;
    :Review Order Details;
    :Change status to 'ready';
    :Coordinate with kitchen/bar;
    :Change status to 'processed';
    stop
```

## Payment Process
```mermaid
activityDiagram
    start
    :Select Active Transaction;
    :Click 'Pay' button;
    if (Payment Method?) then (QRIS)
        :Generate QRIS Code;
        :Wait for Midtrans Notification;
        if (Payment Success?) then (Yes)
            :Update status to completed;
        else (No)
            :Retry or Change Method;
        endif
    else (Cash)
        :Input amount received;
        :Calculate change;
        :Update status to completed;
    endif
    :Print Receipt;
    stop
```
