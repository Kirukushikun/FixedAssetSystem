@if(request()->is('dashboard*'))
    <x-page-hint type="what" title="Dashboard Overview">
        <li>At-a-glance summary of your fixed asset portfolio across assigned farms</li>
        <li>Cards show totals for available, issued, disposed, and flagged assets</li>
        <li>Condition chart breaks assets down by Good, Defective, Repair, and Replace</li>
        <li>Repair summary tracks maintenance activity and total cost year-to-date</li>
        <li>Data is scoped to your farm; admins see all farms</li>
    </x-page-hint>

@elseif(request()->is('assetmanagement/create*'))
    <x-page-hint type="help" title="Creating a New Asset">
        <li><strong>Category &amp; Sub-category</strong> — required; classifies the asset and cannot be changed after saving</li>
        <li><strong>Brand &amp; Model</strong> — manufacturer name and model identifier (required)</li>
        <li><strong>Status</strong> — set Available if unassigned, Issued if handed to an employee</li>
        <li><strong>Condition</strong> — current physical state: Good, Defective, Repair, or Replace</li>
        <li><strong>Acquisition Date</strong> — when the asset was purchased or received</li>
        <li><strong>Farm &amp; Department</strong> — determines which users can see this asset</li>
    </x-page-hint>

@elseif(request()->is('assetmanagement/edit*'))
    <x-page-hint type="help" title="Editing an Asset">
        <li>Reference ID, Category, and Sub-category are locked after creation</li>
        <li>Setting status to <strong>Lost</strong> automatically opens a Lost Asset Investigation</li>
        <li>Setting status to <strong>For Disposal</strong> queues the asset for the disposal workflow</li>
        <li>All field changes are logged automatically in the asset's History tab</li>
        <li>Attachments, technical data, and remarks can be updated at any time</li>
    </x-page-hint>

@elseif(request()->is('assetmanagement/view*'))
    <x-page-hint type="what" title="Asset Detail">
        <li>Shows the complete asset record including technical specifications</li>
        <li><strong>History</strong> — every field change with who made it and when</li>
        <li><strong>Repairs</strong> — all maintenance records with type, cost, and service date</li>
        <li><strong>Audits</strong> — physical verification entries with condition and notes</li>
        <li>An alert banner appears at the top if a Lost Asset Investigation is open</li>
    </x-page-hint>

@elseif(request()->is('assetmanagement/audit*'))
    <x-page-hint type="help" title="Auditing an Asset">
        <li><strong>Condition</strong> — required; record the physical state observed during this check</li>
        <li><strong>Notes</strong> — optional but recommended if anything changed since the last audit</li>
        <li>Audited By and Audited At are filled automatically from your account and current time</li>
        <li>Audit records are permanent and cannot be edited or deleted after submission</li>
    </x-page-hint>

@elseif(request()->is('assetmanagement/qr*'))
    <x-page-hint type="what" title="QR Code Management">
        <li>View and manage QR label status for all assets in your farm</li>
        <li><strong>QR Printed</strong> — mark once a label has been generated and printed</li>
        <li><strong>QR Affixed</strong> — mark once the physical sticker has been attached to the device</li>
        <li>Scanning any QR code redirects directly to that asset's View page</li>
    </x-page-hint>

@elseif(request()->is('assetmanagement*'))
    <x-page-hint type="what" title="Asset Registry">
        <li>Lists all fixed assets you have access to, scoped by your farm and role</li>
        <li>Search by Reference ID, brand, model, serial number, or assigned name</li>
        <li>Use the filter icon to narrow results by category, status, condition, or farm</li>
        <li>Click ⋮ on any row to View, Audit, or Edit an asset</li>
        <li>Disposed assets are locked from editing</li>
    </x-page-hint>

@elseif(request()->is('transfer-workspace*'))
    <x-page-hint type="what" title="Transfer Workspace">
        <li><strong>Request Transfer</strong> — submit a new request to move an asset to another employee</li>
        <li><strong>Pending</strong> — view your submitted requests and their current approval status</li>
        <li><strong>DH Approval</strong> — Division Heads approve or reject pending transfer requests</li>
        <li><strong>Accounting</strong> — completes approved transfers and updates the asset record</li>
        <li>Tick External Transfer to move an asset across farms or departments</li>
    </x-page-hint>

@elseif(request()->is('disposal-workspace*'))
    <x-page-hint type="what" title="Disposal Workspace">
        <li><strong>Disposal Request</strong> — submit a request to dispose of an asset with written justification</li>
        <li><strong>DH Approval</strong> — Division Heads review and approve or reject disposal requests</li>
        <li><strong>VP Approval</strong> — VP reviews DH-approved requests before final processing</li>
        <li><strong>Accounting</strong> — processes final disposal; supports bulk-disposing multiple assets at once</li>
        <li>Only assets with status Available or For Disposal can be submitted</li>
    </x-page-hint>

@elseif(request()->is('sme-workspace*'))
    <x-page-hint type="what" title="SME Review Workspace">
        <li>Subject Matter Experts review and assess assets within their assigned scope</li>
        <li>Complete the review form for each asset to log condition, usability, and remarks</li>
        <li>Reviews are dated and tied to your account for audit and compliance purposes</li>
        <li>Completing a review updates the asset's SME status and last-reviewed date</li>
    </x-page-hint>

@elseif(request()->is('it-analytics*'))
    <x-page-hint type="what" title="IT Analytics">
        <li>Displays IT asset metrics scoped to your farm; admins see all farms</li>
        <li><strong>Near End-of-Life</strong> — assets whose usable life expires within the next 12 months</li>
        <li><strong>Needs Attention</strong> — assets with poor condition, overdue audits, or open issues</li>
        <li><strong>6-Month Trend</strong> — monthly breakdown of acquisitions, repairs, costs, and disposals</li>
        <li>Click <em>Generate Insights</em> for an AI-powered analysis; cached per day per farm</li>
    </x-page-hint>

@elseif(request()->is('employees*'))
    <x-page-hint type="what" title="Employee Directory">
        <li>Lists all employees available for asset assignment across farms</li>
        <li>An employee must exist here before they can be assigned to an asset</li>
        <li>Search or filter by farm and department to find the right person quickly</li>
        <li>View an employee's profile to see all assets currently assigned to them</li>
    </x-page-hint>

@elseif(request()->is('systemrecords*'))
    <x-page-hint type="what" title="System Records">
        <li><strong>Audit Trail</strong> — logs every create, update, and delete action on assets with full detail</li>
        <li><strong>User Logs</strong> — records login and logout activity with timestamps per user</li>
        <li><strong>User Access</strong> — view and manage role assignments for all system users</li>
        <li><strong>Trash</strong> — soft-deleted assets that can be restored if removed by mistake</li>
    </x-page-hint>

@elseif(request()->is('settings*'))
    <x-page-hint type="about" title="Settings &amp; Access Control">
        <li>Manage user accounts and the roles assigned to each user</li>
        <li>Roles bundle permissions together; assign one or more roles per user</li>
        <li>Permissions control access to individual modules and specific actions</li>
        <li>Permission changes take effect immediately — no restart or re-login required</li>
        <li>Admin accounts always have full access regardless of permission assignments</li>
    </x-page-hint>
@endif
