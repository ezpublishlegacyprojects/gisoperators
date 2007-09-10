{def $search_text=ezhttp( 'SearchText','post' )}
{def $range=ezhttp( 'range','post' )}

<div style="border: 1px solid #ff0000; padding: 20px;">
<form action={"/"|ezurl} method="post">
    Brennholz-Händler Suche (PLZ oder Ort)
     <input class="halfbox" type="text" size="20" name="SearchText" id="Search" value="{$search_text|wash}" />
    <input class="button" name="SearchButton" type="submit" value="{'Search'|i18n('design/base')}" /><br/>

    Umkreis:
    <input type="radio" name="range" value="10" {if eq($range,'10')}checked{/if}>10 km</input>
       <input type="radio" name="range" value="50" {if eq($range,'50')}checked{/if}>50 km</input>
       <input type="radio" name="range" value="100" {if eq($range,'100')}checked{/if}>100 km</input>
       <input type="radio" name="range" value="200" {if eq($range,'200')}checked{/if}>200 km</input>
       <input type="radio" name="range" value="500" {if eq($range,'500')}checked{/if}>500 km</input> 

</form>
</div>
<div>

{if ezhttp( 'SearchText','post' )}

Händler im Umkreis von {$range} km<br/>

{def $result = gisrange($search_text,$range)} 

{if $result}
    {foreach $result as $eintrag}
        <div style="border-bottom: 1px solid #c0c0c0;">
            <b>Entfernung: {$eintrag.Distance|round} km</b><br/>
            {def $haendler=fetch( 'content', 'node', hash( 'node_id', $eintrag.node_id ) )}
            {$haendler.data_map.first_name.content} {$haendler.data_map.last_name.content}<br/>
            {$eintrag.street}<br/>
            {$eintrag.zip} {$eintrag.city}<br/>
            {$eintrag.state}<br/>
            {$eintrag.country}<br/>
        </div>
    {/foreach}
{else}
    Es konnte Kein Suchergebniss erzeugt werden
{/if}

{/if}
</div>