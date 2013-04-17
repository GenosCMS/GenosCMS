        {if isset($formErrors)}
        <div class="alert">
            {$formErrors|implode:'<br>'}
        </div>
        {/if}
        <form method="post">
            <fieldset>
                <legend>Validar formulario</legend>
                <div class="control-group">
                    <div class="control-label">Username:</div>
                    <div class="controls">
                        {form_input name='username'}
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">Nombre:</div>
                    <div class="controls">
                        {form_input name='name'}
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">Contraseña:</div>
                    <div class="controls">
                        {form_input name='pwd' type='password'}
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">Confirmar contraseña:</div>
                    <div class="controls">
                        {form_input name='pwd2' type='password'}
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">Año nacimiento:</div>
                    <div class="controls">
                        {form_input name='year'}
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">URL:</div>
                    <div class="controls">
                        {form_input name='url[]'}
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">URL:</div>
                    <div class="controls">
                        {form_input name='url[]'}
                    </div>
                </div>
                <div class="form-actions">
                    <input type="submit" value="Enviar" class="btn btn-primary" />
                </div>
            </fieldset>
        </form>