<div class="table-container h-full flex flex-col" id="logs">
    <table>
            <thead>
                <tr>
                    <th>EMAIL</th>
                    <th>SUCCESS</th>
                    <th>IP ADDRESS</th>
                    <th>USER AGENT</th>
                    <th>DATE</th>
                    <th>TIME</th>
                </tr>
            </thead>
            <tbody>
                @forelse($userLogs as $log)
                <tr>
                    <td>{{$log->email}} <i class="fa-regular fa-copy cursor-pointer text-gray-400"></i></td>
                    <td>{{$log->success}}</td>
                    <td>{{$log->ip_address}}</td>
                    <td>{{$log->user_agent}}</td>
                    <td>{{$log->created_at->format('d/m/Y')}}</td>
                    <td>{{$log->created_at->format('h:i A')}}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                        No user log record found
                    </td>
                </tr>
                @endforelse
            </tbody>
    </table>

    <x-pagination :paginator="$userLogs" />
</div>