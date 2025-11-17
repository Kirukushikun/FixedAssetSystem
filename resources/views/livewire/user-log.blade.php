<div class="table-container tab-content h-full flex flex-col hidden" id="logs">
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
                @foreach($userLogs as $log)
                <tr>
                    <td>#i.guno@bfcgroup <i class="fa-regular fa-copy cursor-pointer text-gray-400"></i></td>
                    <td>True</td>
                    <td>192.123.4.5</td>
                    <td>BCD-1234</td>
                    <td>Oct 31, 2025</td>
                    <td>12:00 NN</td>
                </tr>
                @endforeach
            </tbody>
    </table>

    <x-pagination :paginator="$userLogs" />
</div>