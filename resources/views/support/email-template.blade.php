<h2>📩 New Support Request</h2>

<p><strong>Subject:</strong> {{ $subject }}</p>
<p><strong>Message:</strong></p>
<p>{!! nl2br(e($messageContent)) !!}</p>

<hr>

<h4>🔎 User Context</h4>
<ul>
    <li><strong>User ID:</strong> {{ $context['user_id'] }}</li>
    <li><strong>Name:</strong> {{ $context['name'] }}</li>
    <li><strong>Email:</strong> {{ $context['email'] }}</li>
    <li><strong>Account Status:</strong> {{ $context['account_status'] }}</li>
    <li><strong>IP Address:</strong> {{ $context['ip_address'] }}</li>
    <li><strong>Browser / Device:</strong> {{ $context['user_agent'] }}</li>
    <li><strong>Submitted At:</strong> {{ $context['submitted_at'] }}</li>
</ul>
