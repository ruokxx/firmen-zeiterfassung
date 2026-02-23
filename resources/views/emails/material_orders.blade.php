<x-mail::message>
# Offene Materialbestellungen

Folgende Materialien müssen noch bestellt werden:

<x-mail::table>
| Material | Name des Mitarbeiters |
| :--- | :--- |
@foreach($orders as $order)
| {{ $order->item_name }} | {{ optional($order->user)->name ?? 'Unbekannt' }} |
@endforeach
</x-mail::table>

Bitte prüfen und bestellen.

<x-mail::button :url="route('material-orders.index')">
Zu den Materialbestellungen
</x-mail::button>

Mit freundlichen Grüßen,<br>
{{ config('app.name') }}
</x-mail::message>
