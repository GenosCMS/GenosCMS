            <h3>Configuración de la base de datos</h3>
            <hr class="hr-condensed" />
            <form method="post" action="" class="form-horizontal">
                <div class="control-group">
                    <div class="control-label">
                        <label>Tipo de base de datos:</label>
                    </div>
                    <div class="controls">
                        {form_select name='driver' options=$drivers}
                        <p class="help-block">Se recomienda MySQLi</p>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <label>Servidor:</label>
                    </div>
                    <div class="controls">
                        {form_input name='host' value='localhost' type='text'}
                        <p class="help-block">Usualmente "localhost"</p>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <label>Usuario:</label>
                    </div>
                    <div class="controls">
                        {form_input name='host' type='text'}
                        <p class="help-block">Algo como "root" o un nombre de usuario facilitado por quien te brinda el hospedaje.</p>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <label>Contraseña:</label>
                    </div>
                    <div class="controls">
                        {form_input name='pass' type='password'}
                        <p class="help-block">Por cuestiones de seguridad, es primordial usar una contraseña para la cuenta de su base de datos.</p>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <label>Base de datos:</label>
                    </div>
                    <div class="controls">
                        {form_input name='name' type='text'}
                        <p class="help-block">En algunos hospedajes solo se permite una base de datos por sitio. En esos casos, si le interesa instalar más de un sitio, puede usar el prefijo de las tablas para distinguir entre los sitios de <strong>Genos CMS</strong> que usen la misma base de datos.</p>
                    </div>
                </div>
                <div class="control-group">
                    <div class="control-label">
                        <label>Prefijo de las tablas:</label>
                    </div>
                    <div class="controls">
                        {form_input name='prefix' value=$prefix}
                        <p class="help-block">Elija un prefijo para la base de datos o use el <b>generado aleatoriamente</b>. Lo óptimo es que sea de tres o cuatro caracteres de largo y que contenga solo caracteres alfanuméricos, y DEBE acabar con un guión bajo. <b>Asegúrese de que el prefijo elegido no esté siendo usado por otras tablas</b>.</p>
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <fieldset id="jform_site_offline" class="radio btn-group">
                            {form_radio name='site_offline' value='0' id='jform_site_offline0' checked='true'}
                            <label for="jform_site_offline0">No</label>
                            {form_radio name='site_offline' value='1' id='jform_site_offline1'}
                            <label for="jform_site_offline1">Sí</label>
                        </fieldset>
                    </div>
                </div>
                <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Siguiente <i class="icon-arrow-right icon-white"></i></button>
                </div>
            </form>