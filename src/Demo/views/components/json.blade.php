<code id="code"
      class="block bg-slate-800 rounded p-8 text-yellow-300 whitespace-pre-wrap my-6 min-h-[600px]">

</code>
@if(!isset($noscript))
<script>
    document.getElementById('code').innerHTML = prettyPrintJson.toHtml({!! $results ?? '' !!});
</script>
@endif

