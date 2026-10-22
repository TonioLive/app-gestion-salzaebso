<?php if ($permitido === true) { ?>
            </main>
            <footer class="app-footer">
                <span>Gestión de Escuelas Infantiles · Lápices</span>
                <span>Área de personal</span>
            </footer>
        </div>
        <button class="sidebar-backdrop" type="button" aria-label="Cerrar menú" data-sidebar-backdrop></button>
    </div>
<?php } ?>
<script src="/gestion/panel/assets/panel.js?v=20260728.1" defer></script>
<?php if (!empty($panel_extra_scripts) && is_array($panel_extra_scripts)) { ?>
    <?php foreach ($panel_extra_scripts as $panel_extra_script) { ?>
        <script src="<?php echo app_escape($panel_extra_script); ?>" defer></script>
    <?php } ?>
<?php } ?>
</body>
</html>
