@php
    $sold = (int) ($row['sold'] ?? 0);
@endphp

<div class="ticket-edit" data-ticket-row>
    @if (! empty($row['id']))
        <input type="hidden" name="tickets[{{ $i }}][id]"
            value="{{ $row['id'] }}">
    @endif

    <label>Name
        <input type="text" name="tickets[{{ $i }}][name]" required
            value="{{ $row['name'] }}" placeholder="e.g. VIP">
    </label>

    <label>Price (₦)
        <input type="number" name="tickets[{{ $i }}][price]"
            min="0" step="0.01" required value="{{ $row['price'] }}">
    </label>

    <label>Quantity
        <input type="number" name="tickets[{{ $i }}][quantity]"
            min="{{ max(1, $sold) }}" required
            value="{{ $row['quantity'] }}">
    </label>

    <label>Max / order
        <input type="number" name="tickets[{{ $i }}][max_per_order]"
            min="1" max="20" value="{{ $row['max_per_order'] ?? 10 }}">
    </label>

    @if ($sold > 0)
        <span class="hint sold-note">{{ $sold }} sold</span>
    @else
        <button type="button" class="link-btn danger"
            data-remove-ticket title="Remove">&times;</button>
    @endif
</div>
