<div id="patternlab_header">
	<% require css('pattern-lab/css/patternlab.css') %>
	<h1>Pattern Lab ($PatternName)</h1>

	<p><a href="patterns/">See all</a> <% if PreviousPattern %><a href="$PreviousPattern.Link">(Previous: $PreviousPattern.Name)</a><% end_if %> <% if NextPattern %><a href="$NextPattern.Link">(Next: $NextPattern.Name)</a><% end_if %></p>

</div>