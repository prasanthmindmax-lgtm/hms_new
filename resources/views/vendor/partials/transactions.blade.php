<table class="table table-bordered">
    <thead>
        <tr>
            <th>Date</th>
            <th>Nature</th>
            <th>Type</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        @forelse($transactions as $t)
            <tr>
                <td>{{ $t['date'] }}</td>
                <td>{{ $t['nature'] }}</td>
                <td>{{ $t['type'] }}</td>
                <td>{{ $t['amount'] }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="text-center">No Data Found</td></tr>
        @endforelse
    </tbody>
</table>
