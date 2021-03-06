<div class="row">
  <div class="col-sm-8">
    <h2>Bandwidth Usage</h2>
  </div>
  <div class="col-sm-4">
    <form method="post" action="clientarea.php?action=productdetails">
      <input type="hidden" name="id" value="{$budgetvm->service}" />
      <button type="submit" class="btn btn-default btn-block"><i class="fa fa-arrow-circle-left"></i> Back to Overview</button>
    </form>
  </div>
</div>

<div class="row">
  <div class="col-sm-12">
    <ul>
      <li>Bandwidth Usage: {$budgetvm->bandwidth->result->used->total_gb} GB</li>
    </ul>
    {$budgetvm->bandwidth->result->graph_img}
  </div>
</div>

{if $budgetvm->type == "dedicated"}
<div class="row">
  <div class="col-sm-12">
    <h4>Graph Period</h4>
    <select id="period" onchange="window.location='clientarea.php?action=productdetails&id={$budgetvm->service}&customAction=network&period=' + this.options[this.selectedIndex].value;" class="form-control">
    {if isset($period)}
      {if $period == "hour"}
      <option value="hour" checked>--> Last Hour</option>
      {elseif $period == "day"}
      <option value="day" checked>--> Last Day</option>
      {elseif $period == "week"}
      <option value="week" checked>--> Last Week</option>
      {elseif $period == "month"}
      <option value="month" checked>--> Last Month</option>
      {elseif $period == "year"}
      <option value="year" checked>--> Last Year</option>		
      {/if}
    {else}
      <option value="month" checked>--> Last Month</option>
    {/if}
  		<option value="hour">Last Hour</option>
        <option value="day">Last Day</option>
        <option value="week">Last Week</option>
        <option value="month">Last Month</option>
        <option value="year">Last Year</option>
      </select>
  </div>
</div>
{/if}