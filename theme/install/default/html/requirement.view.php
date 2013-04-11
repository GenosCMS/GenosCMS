            <h3>Comprobar requisitos</h3>
            <hr class="hr-condensed" />
            {foreach from=$checks item=group}
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th colspan="2">{$group.title}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$group.checks key=check item=value}
                    <tr>
                        <td>{$check}</td>
                        <td style="width: 30%;">{if $value}{$group.passed}{elseif $check == 'include\setting\server.php'}{$group.rename}{else}{$group.failed}{/if}</td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
            {/foreach}
            <hr class="hr-condensed" />
            {if $isPassed}
            <form method="post" action="">
                <input type="hidden" name="passed" value="1" />
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Siguiente <i class="icon-arrow-right icon-white"></i></button>
                </div>
            </form>
            {else}
            <a href="#" onclick="window.location.href = ''" class="btn btn-danger"><i class="icon-refresh icon-white"></i> Actualizar</a>
            {/if}