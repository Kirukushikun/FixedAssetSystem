<div class="table-container h-full flex flex-col">
    <table>
            <thead>
                <tr>
                    <th>USER ID</th>
                    <th>USER NAME</th>
                    <th>ACTION</th>
                    <th>DATE</th>
                    <th>TIME</th>
                </tr>
            </thead>
            <tbody>
                @foreach($audits as $audit)
                    <tr>
                        <td>#{{$audit->user_id}} <i class="fa-regular fa-copy cursor-pointer text-gray-400"></i></td>
                        <td>{{$audit->user_name}}</td>
                        <td>{{$audit->action}}</td>
                        <td>{{$audit->created_at->format('d/m/Y')}}</td>
                        <td>{{$audit->created_at->format('h:i A')}}</td>
                    </tr>
                @endforeach
            </tbody>
    </table>

    <x-pagination :paginator="$audits" />
</div>