<?php
// Render only the roster belonging to a permission-filtered group.
if(!defined('POOL_CALENDAR_VIEW')){http_response_code(404);exit;}
?>
<article class="card group-card">
 <div class="group-top"><span class="eyebrow"><?php echo $days[(int)$g['dia']]; ?></span><span class="pill <?php echo $full?'full':''; ?>"><?php echo $g['bloqueado']?'Bloqueado':($full?'Completo':max(0,$cap-$used).' libres'); ?></span></div>
 <h2><?php echo app_escape(substr($g['hora_inicio'],0,5).' – '.substr($g['hora_fin'],0,5)); ?></h2>
 <h3><?php echo app_escape($g['nombre_tipo_grupo']?:'Natación'); ?></h3>
 <p class="group-team"><?php echo isset($team[$k])?app_escape(implode(', ',$team[$k])):'Monitor pendiente de asignar'; ?></p>
 <div class="occupancy"><span><?php echo $used; ?> / <?php echo $cap; ?> plazas</span><span>Grupo <?php echo app_escape($k); ?></span></div>
 <progress max="<?php echo max(1,$cap,$used); ?>" value="<?php echo $used; ?>" aria-label="Ocupación del grupo"></progress>
 <details class="calendar-roster"><summary>Ver alumnos <span>(<?php echo $used; ?>)</span></summary>
  <ul class="roster calendar-students">
  <?php foreach($list as $s){
   $studentName=trim($s['Nombre'].' '.$s['Apellido1'].' '.$s['Apellido2']);
   $payment=$studentPayments[(int)$s['id_alumno']]??null;
   $pending=$payment&&$payment['status']==='pending';
   $canLink=$canOpenStudentFile&&security_owned_centre((int)$s['id_centro']);
  ?>
   <li class="calendar-student<?php if($pending)echo ' has-pending-payment'; ?>" data-student-name="<?php echo app_escape($studentName); ?>">
    <div class="calendar-student-heading">
     <?php if($canLink){ ?><a class="calendar-student-name" href="/alumnos/nuevo_alumno1.php?u=<?php echo (int)$s['id_usuario']; ?>&amp;curso_seleccionado=<?php echo (int)$course; ?>" aria-label="<?php echo app_escape('Abrir ficha de '.$studentName); ?>"><?php echo app_escape($studentName); ?></a><?php }else{ ?><span class="calendar-student-name"><?php echo app_escape($studentName); ?></span><?php } ?>
     <span class="calendar-search-badge" hidden>Alumno buscado</span>
    </div>
    <?php if($payment){ ?>
     <span class="calendar-payment-status <?php echo $pending?'is-pending':($payment['status']==='paid'?'is-paid':'is-uninvoiced'); ?>">
      <?php if($pending){ ?><strong>Pendiente · <?php echo number_format($payment['pending_cents']/100,2,',','.'); ?> €</strong><small><?php echo (int)$payment['pending_invoices']; ?> <?php echo $payment['pending_invoices']===1?'recibo/factura con saldo pendiente':'recibos/facturas con saldo pendiente'; ?> de esta matrícula</small><?php }elseif($payment['status']==='paid'){ ?>Al día<?php }else{ ?>Sin facturas<?php } ?>
     </span>
    <?php } ?>
   </li>
  <?php } if(!$list)echo '<li class="calendar-roster-empty">Sin alumnos asignados.</li>'; ?>
  </ul>
  <?php if($list&&$studentPayments){ ?><p class="calendar-payment-help">Estado de los recibos y facturas registrados en este curso.</p><?php } ?>
 </details>
 <div class="group-actions">
  <a class="primary" href="programacion.php?grupo=<?php echo (int)$g['id_grupo']; ?>&amp;sub=<?php echo (int)$g['id_subgrupo']; ?>">Programación y evaluación →</a>
  <?php if(pool_manager()){ ?><a class="quiet" href="/gestion/grupos_pruebas/detalles_grupo.php?id_grupo=<?php echo (int)$g['id_grupo']; ?>&amp;id_subgrupo=<?php echo (int)$g['id_subgrupo']; ?>&amp;fecha_limite=<?php echo date('Y-m-d'); ?>">Gestionar alumnos →</a>
  <details><summary>Plazas</summary><form class="capacity-form" method="post"><input type="hidden" name="_csrf" value="<?php echo app_csrf_token(); ?>"><input type="hidden" name="action" value="capacity"><input type="hidden" name="subgroup" value="<?php echo (int)$g['id_grupo_subgrupo']; ?>"><label>Capacidad<input type="number" name="capacity" min="1" max="100" value="<?php echo $cap; ?>" required></label><button class="primary">Guardar</button></form></details><?php } ?>
 </div>
</article>
