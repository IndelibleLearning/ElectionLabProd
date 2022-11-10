<link rel="stylesheet" href="css/game-deployments.css">

<div id="deployments_area" class="hidden deployments-area elo-action-screen">
      <div id="deploy_pieces_area" class="hidden">
          <h1 class="deployments-title elo-header">Deploy Campaign Resources</h1>
    	  <div class="resource-num">
    	      <span>Campaign Resources Left:</span> 
    	      <span id="total_pieces">24</span>
    	  </div>
        <ul id="input-state-deployments" class="input-state-deployments">
            <li>
                <input type="range" class="state-slider" orient="vertical" min="0" max="5" step="1" value="0" id="NH_pieces" data-abbrev="NH"><label for="NH_pieces" class="state-slider-label">NH (4)</label><div id="dNH"class="state-pip-display">0</div><br>
            </li>
            <li>
                <input type="range" class="state-slider" orient="vertical" min="0" max="5" step="1" value="0" id="NV_pieces" data-abbrev="NV"><label for="NV_pieces" class="state-slider-label">NV (6)</label><div id="dNV"class="state-pip-display">0</div><br>
            </li>
            <li>
                <input type="range" class="state-slider" orient="vertical" min="0" max="5" step="1" value="0" id="CO_pieces" data-abbrev="CO"><label for="CO_pieces" class="state-slider-label">CO (9)</label><div id="dCO"class="state-pip-display">0</div><br>
            </li>
            <li>
                <input type="range" class="state-slider" orient="vertical" min="0" max="5" step="1" value="0" id="MN_pieces" data-abbrev="MN"><label for="MN_pieces" class="state-slider-label">MN (10)</label><div id="dMN"class="state-pip-display">0</div><br>
            </li>
            <li>
                <input type="range" class="state-slider" orient="vertical" min="0" max="5" step="1" value="0" id="WI_pieces" data-abbrev="WI"><label for="WI_pieces" class="state-slider-label">WI (10)</label><div id="dWI"class="state-pip-display">0</div><br>
            </li>
            <li>
                <input type="range" class="state-slider" orient="vertical" min="0" max="5" step="1" value="0" id="AZ_pieces" data-abbrev="AZ"><label for="AZ_pieces" class="state-slider-label">AZ (11)</label><div id="dAZ"class="state-pip-display">0</div><br>
            </li>
            <li>
                <input type="range" class="state-slider" orient="vertical" min="0" max="5" step="1" value="0" id="VA_pieces" data-abbrev="VA"><label for="VA_pieces" class="state-slider-label">VA (13)</label><div id="dVA"class="state-pip-display">0</div><br>
            </li>
            <li>
                <input type="range" class="state-slider" orient="vertical" min="0" max="5" step="1" value="0" id="NC_pieces" data-abbrev="NC"><label for="NC_pieces" class="state-slider-label">NC (15)</label><div id="dNC"class="state-pip-display">0</div><br>
            </li>
            <li>
                <input type="range" class="state-slider" orient="vertical" min="0" max="5" step="1" value="0" id="GA_pieces" data-abbrev="GA"><label for="GA_pieces" class="state-slider-label">GA (16)</label><div id="dGA"class="state-pip-display">0</div><br>
            </li>
            <li>
                <input type="range" class="state-slider" orient="vertical" min="0" max="5" step="1" value="0" id="MI_pieces" data-abbrev="MI"><label for="MI_pieces" class="state-slider-label">MI (16)</label><div id="dMI"class="state-pip-display">0</div><br>
            </li>
            <li>
                <input type="range" class="state-slider" orient="vertical" min="0" max="5" step="1" value="0" id="PA_pieces" data-abbrev="PA"><label for="PA_pieces" class="state-slider-label">PA (20)</label><div id="dPA"class="state-pip-display">0</div><br>
            </li>
            <li>
                <input type="range" class="state-slider" orient="vertical" min="0" max="5" step="1" value="0" id="FL_pieces" data-abbrev="FL"><label for="FL_pieces" class="state-slider-label">FL (29)</label><div id="dFL"class="state-pip-display">0</div><br>
            </li>
    </ul> 
        <button id="submit_deployments" class="elo-button">
            Submit
        </button> 
    </div>
  </div>