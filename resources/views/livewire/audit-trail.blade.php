<div class="table-container tab-content h-full flex flex-col" id="audit">
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
                        <td>#1553 <i class="fa-regular fa-copy cursor-pointer text-gray-400"></i></td>
                        <td>Chris Bacon</td>
                        <td>Asset Created</td>
                        <td>Oct 31, 2025</td>
                        <td>12:00 NN</td>
                    </tr>
                @endforeach
            </tbody>
    </table>

    <x-pagination :paginator="$audits" />
</div>