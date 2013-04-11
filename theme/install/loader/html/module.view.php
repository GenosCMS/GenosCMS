            {if $isAdded}
            <div class="alert alert-success">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                <h4>Felicidades!</h4>
                <p>El módulo fué cargado correctamente</p>
            </div>
            {/if}
            <h3>Administrar módulos <div class="pull-right"><a href="#" class="btn btn-small" onclick="showDiv('addModule'); return false">Agregar módulo</a></div></h3>
            <hr class="hr-condensed" />
            <table class="table">
                <thead>
                    <tr>
                        {foreach from=$modules[0] key=title item=module}
                        <th>{$title}</th>
                        {/foreach}
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$modules item=module}
                    <tr>
                        {foreach from=$module item=mod}
                        <td>{$mod}</td>
                        {/foreach}
                    </tr>
                    {/foreach}
                </tbody>
            </table>
            <div id="addModule" class="hide">
                <h3>Agrega módulo</h3>
                <hr class="hr-condensed" />
                <form method="post" action="" class="form-horizontal">
                    {foreach from=$modules[0] key=title item=module}
                    <div class="control-group">
                        <div class="control-label">
                            <label>{$title}</label>
                        </div>
                        <div class="controls">
                            {form_input name='module['$title']' value=$module}
                        </div>
                    </div>
                    {/foreach}
                    <div class="control-group">
                        <div class="controls">
                            <input type="submit" class="btn btn-success" value="Guardar" />
                        </div>
                    </div>
                </form>
            </div>