<h1>You have successfully imported xls file</h1>

<h2>Import stats:</h2>

<ul>
	<li>All rows: {{ $stats->records }}</li>
	<li>Objects created: {{ $stats->new_objects }}</li>
	<li>Objects updated: {{ $stats->records-$stats->new_objects }}</li>
	<li>Years created: {{ $stats->new_years }}</li>
	<li>Cities created: {{ $stats->new_cities }}</li>
	<li>Cities updated: {{ $stats->old_cities }}</li>
	<li>Streets created: {{ $stats->new_streets }}</li>
	<li>Streets updated: {{ $stats->old_streets }}</li>
</ul>

<p>Updates rows may be not affected. This is just info that object has been found in database.</p>