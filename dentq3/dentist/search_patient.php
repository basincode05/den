<?php
session_start();
// ตรวจสอบสิทธิ์ Dentist
if($_SESSION['role'] != 'Dentist'){
    header("Location: ../index.php");
    exit();
}
require_once '../includes/db.php';

$search_keyword = "";
$patients = [];

// หากมีการค้นหา
if(isset($_GET['q'])) {
    $search_keyword = $conn->real_escape_string(trim($_GET['q']));
    $sql_search = "SELECT * FROM Patient 
                   WHERE Name_Surname LIKE '%$search_keyword%' OR Phone LIKE '%$search_keyword%'";
    $res_search = $conn->query($sql_search);
    if($res_search && $res_search->num_rows > 0) {
        while($r = $res_search->fetch_assoc()) {
            $patients[] = $r;
        }
    }
}

// หากมีการคลิกดูประวัติของผู้ป่วยรายบุคคล
$selected_patient = null;
$history_records = [];
if(isset($_GET['patient_id'])) {
    $p_id = (int)$_GET['patient_id'];
    
    // ดึงข้อมูลผู้ป่วย
    $res_p = $conn->query("SELECT * FROM Patient WHERE Patient_ID = $p_id");
    if($res_p->num_rows > 0) $selected_patient = $res_p->fetch_assoc();

    // ดึงข้อมูลประวัติการรักษา
    $hist_sql = "
        SELECT a.Appt_Date, a.Time_Slot, s.Service_Name, tr.Note, tr.Next_Appt_Date,
               td.Tooth_ID, prob.Problem_Name, tm.Treatment_Name
        FROM Appointment a
        JOIN Service s ON a.Service_ID = s.Service_ID
        JOIN Treatment_Record tr ON a.Appointment_ID = tr.Appointment_ID
        LEFT JOIN Treatment_Detail td ON tr.Record_ID = td.Record_ID
        LEFT JOIN Problem prob ON td.Problem_ID = prob.Problem_ID
        LEFT JOIN Treatment_Method tm ON td.Treatment_Method_ID = tm.Treatment_Method_ID
        WHERE a.Patient_ID = $p_id AND a.Status = 'Done'
        ORDER BY a.Appt_Date DESC, a.Time_Slot DESC
    ";
    $res_h = $conn->query($hist_sql);
    if($res_h) {
        while($row = $res_h->fetch_assoc()) {
            $key = $row['Appt_Date'] . '_' . $row['Time_Slot'];
            if(!isset($history_records[$key])) {
                $history_records[$key] = [
                    'date' => $row['Appt_Date'],
                    'time' => $row['Time_Slot'],
                    'service' => $row['Service_Name'],
                    'note' => $row['Note'],
                    'next_date' => $row['Next_Appt_Date'],
                    'details' => []
                ];
            }
            if($row['Tooth_ID']) {
                $history_records[$key]['details'][] = [
                    'tooth' => $row['Tooth_ID'],
                    'problem' => $row['Problem_Name'],
                    'treatment' => $row['Treatment_Name']
                ];
            }
        }
    }
}

include '../includes/header.php'; 
?>

<!-- Premium Banner -->
<div style="background: linear-gradient(135deg, #1e293b, #334155); border-radius: 28px; padding: 40px 50px; color: white; margin-bottom: 40px; box-shadow: 0 20px 40px rgba(30, 41, 59, 0.3); position: relative; overflow: hidden; display: flex; align-items: center; justify-content: space-between; border: 1px solid rgba(255,255,255,0.1);">
    <div style="position: absolute; top: -50px; right: 50px; width: 250px; height: 250px; background: radial-gradient(circle, rgba(59, 130, 246, 0.2) 0%, transparent 70%); border-radius: 50%;"></div>
    
    <div style="position: relative; z-index: 1;">
        <div style="display: inline-block; background: rgba(59, 130, 246, 0.2); color: #60a5fa; padding: 8px 16px; border-radius: 20px; font-weight: 600; font-size: 0.9rem; margin-bottom: 15px; border: 1px solid rgba(59, 130, 246, 0.3);">
            <i data-lucide="search" style="width: 16px; height: 16px; margin-right: 5px;"></i> ค้นหาประวัติคนไข้
        </div>
        <h2 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 10px; letter-spacing: -1px;">ระบบสืบค้นแฟ้มประวัติผู้ป่วย</h2>
        <p style="font-size: 1.1rem; color: #cbd5e1; max-width: 600px;">ค้นหาผู้ป่วยด้วยชื่อหรือเบอร์โทรศัพท์ เพื่อดูประวัติการรักษาและการบันทึกฟันรายซี่ย้อนหลังทั้งหมด</p>
    </div>

    <i data-lucide="folder-search" style="width: 150px; height: 150px; color: rgba(255,255,255,0.05); position: absolute; right: 40px; transform: rotate(10deg);"></i>
</div>

<div style="max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: 1fr 2fr; gap: 30px; align-items: start;">
    
    <!-- ฝั่งซ้าย: ค้นหาและผลลัพธ์ -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        
        <!-- กล่องค้นหา -->
        <div class="booking-container" style="padding: 25px !important;">
            <h3 style="color: var(--text-main); margin-bottom: 20px; font-size: 1.2rem;"><i data-lucide="search" style="margin-right: 8px; color: var(--primary);"></i> ค้นหารายชื่อ</h3>
            
            <form action="search_patient.php" method="GET">
                <div class="input-group">
                    <input type="text" name="q" value="<?php echo htmlspecialchars($search_keyword); ?>" placeholder="พิมพ์ชื่อ หรือ เบอร์โทร..." required style="width: 100%; padding: 15px; border-radius: 12px; background: #f8fafc; border: 1px solid #e2e8f0; font-size: 1rem; color: var(--text-main); margin-bottom: 15px;">
                </div>
                <button type="submit" class="btn" style="width: 100%; padding: 15px; font-size: 1rem;">ค้นหาผู้ป่วย</button>
            </form>
        </div>

        <!-- ผลลัพธ์การค้นหา -->
        <?php if(isset($_GET['q'])): ?>
            <div class="booking-container" style="padding: 25px !important;">
                <h4 style="color: var(--text-muted); margin-bottom: 15px; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 1px;">ผลการค้นหา</h4>
                
                <?php if(count($patients) > 0): ?>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <?php foreach($patients as $p): ?>
                            <?php $is_selected = ($selected_patient && $selected_patient['Patient_ID'] == $p['Patient_ID']); ?>
                            <a href="search_patient.php?q=<?php echo urlencode($search_keyword); ?>&patient_id=<?php echo $p['Patient_ID']; ?>" style="text-decoration: none;">
                                <div style="background: <?php echo $is_selected ? 'rgba(59, 130, 246, 0.1)' : '#ffffff'; ?>; padding: 15px; border: 1px solid <?php echo $is_selected ? 'var(--primary)' : '#e2e8f0'; ?>; border-radius: 12px; display: flex; justify-content: space-between; align-items: center; transition: all 0.2s; box-shadow: <?php echo $is_selected ? '0 4px 12px rgba(59,130,246,0.1)' : 'none'; ?>;">
                                    <div>
                                        <h4 style="font-size: 1.05rem; color: var(--text-main); margin-bottom: 5px;"><?php echo htmlspecialchars($p['Name_Surname']); ?></h4>
                                        <p style="font-size: 0.85rem; color: var(--text-muted);"><i data-lucide="phone" style="width: 12px; margin-right: 4px;"></i> <?php echo htmlspecialchars($p['Phone']); ?></p>
                                    </div>
                                    <i data-lucide="chevron-right" style="color: <?php echo $is_selected ? 'var(--primary)' : 'var(--text-muted)'; ?>;"></i>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 30px 10px;">
                        <i data-lucide="user-x" style="width: 40px; height: 40px; color: #cbd5e1; margin-bottom: 10px;"></i>
                        <p style="color: var(--text-muted);">ไม่พบข้อมูลผู้ป่วยที่ค้นหา</p>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- ฝั่งขวา: ประวัติการรักษา -->
    <div>
        <?php if($selected_patient): ?>
            
            <!-- ข้อมูลคนไข้ Header -->
            <div class="booking-container" style="padding: 30px !important; margin-bottom: 20px; display: flex; align-items: center; gap: 20px; background: linear-gradient(to right, white, #f8fafc);">
                <div style="width: 70px; height: 70px; background: linear-gradient(135deg, var(--primary), var(--secondary)); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);">
                    <i data-lucide="user" style="width: 35px; height: 35px; color: white;"></i>
                </div>
                <div>
                    <h3 style="font-size: 1.5rem; color: var(--text-main); margin-bottom: 5px;"><?php echo htmlspecialchars($selected_patient['Name_Surname']); ?></h3>
                    <div style="display: flex; gap: 20px; color: var(--text-muted); font-size: 0.95rem;">
                        <span><strong>HN:</strong> <?php echo str_pad($selected_patient['Patient_ID'], 4, '0', STR_PAD_LEFT); ?></span>
                        <span><i data-lucide="phone" style="width: 14px;"></i> <?php echo htmlspecialchars($selected_patient['Phone']); ?></span>
                    </div>
                </div>
            </div>

            <!-- รายการประวัติ -->
            <div class="booking-container" style="padding: 30px !important;">
                <h3 style="color: var(--text-main); margin-bottom: 25px; font-size: 1.2rem; display: flex; align-items: center;"><i data-lucide="history" style="margin-right: 10px; color: var(--accent);"></i> ประวัติการรักษาย้อนหลัง</h3>
                
                <?php if(count($history_records) > 0): ?>
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <?php foreach($history_records as $record): ?>
                            <div style="border: 1px solid var(--card-border); border-radius: 16px; overflow: hidden; box-shadow: var(--shadow-sm);">
                                <!-- หัวข้อแต่ละรอบการรักษา -->
                                <div style="background: #f8fafc; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--card-border);">
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <div style="background: var(--primary); color: white; padding: 5px 12px; border-radius: 20px; font-size: 0.85rem; font-weight: 700;">
                                            <?php echo date('d/m/Y', strtotime($record['date'])); ?>
                                        </div>
                                        <h4 style="font-size: 1.05rem; color: var(--text-main); margin: 0;"><?php echo htmlspecialchars($record['service']); ?></h4>
                                    </div>
                                    <div style="color: var(--text-muted); font-size: 0.9rem;">
                                        <i data-lucide="clock" style="width: 14px; margin-right: 4px;"></i> <?php echo date('H:i', strtotime($record['time'])); ?> น.
                                    </div>
                                </div>
                                
                                <!-- เนื้อหา -->
                                <div style="padding: 20px; background: white;">
                                    <?php if(count($record['details']) > 0): ?>
                                        <div style="margin-bottom: 20px;">
                                            <h5 style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 10px; text-transform: uppercase;">รายละเอียดรายซี่ (Tooth Records)</h5>
                                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px;">
                                                <?php foreach($record['details'] as $det): ?>
                                                    <div style="background: #f1f5f9; padding: 12px; border-radius: 8px; border-left: 3px solid var(--primary);">
                                                        <strong style="color: var(--text-main); display: block; margin-bottom: 5px;">ซี่ที่ <?php echo htmlspecialchars($det['tooth']); ?></strong>
                                                        <span style="font-size: 0.85rem; color: #ef4444; display: block;">ปัญหา: <?php echo htmlspecialchars($det['problem']); ?></span>
                                                        <span style="font-size: 0.85rem; color: #10b981; display: block;">การรักษา: <?php echo htmlspecialchars($det['treatment']); ?></span>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- โน้ตหมอ -->
                                    <div style="background: #fffbeb; border: 1px solid #fde68a; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                                        <h5 style="font-size: 0.85rem; color: #d97706; margin-bottom: 5px;"><i data-lucide="file-text" style="width: 14px;"></i> บันทึกจากแพทย์ (Notes)</h5>
                                        <p style="font-size: 0.95rem; color: #92400e; margin: 0;"><?php echo htmlspecialchars($record['note']); ?></p>
                                    </div>
                                    
                                    <!-- นัดหมายครั้งต่อไป -->
                                    <?php if($record['next_date']): ?>
                                        <div style="display: flex; align-items: center; gap: 8px; color: var(--primary); font-size: 0.9rem; font-weight: 600;">
                                            <i data-lucide="calendar-forward" style="width: 16px;"></i>
                                            นัดหมายครั้งต่อไป: <?php echo date('d/m/Y', strtotime($record['next_date'])); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px; background: #f8fafc; border-radius: 16px; border: 1px dashed var(--card-border);">
                        <i data-lucide="folder-open" style="width: 45px; height: 45px; color: #cbd5e1; margin-bottom: 15px;"></i>
                        <p style="color: var(--text-muted); font-size: 1.05rem;">ยังไม่มีบันทึกประวัติการรักษาของผู้ป่วยรายนี้</p>
                    </div>
                <?php endif; ?>
            </div>
            
        <?php else: ?>
            <div class="booking-container" style="padding: 60px 40px !important; text-align: center; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center;">
                <i data-lucide="user-search" style="width: 80px; height: 80px; color: #e2e8f0; margin-bottom: 20px;"></i>
                <h3 style="font-size: 1.5rem; color: var(--text-main); margin-bottom: 10px;">เลือกผู้ป่วยเพื่อดูประวัติ</h3>
                <p style="color: var(--text-muted); font-size: 1.1rem; max-width: 400px; line-height: 1.6;">โปรดค้นหาและคลิกเลือกผู้ป่วยจากรายชื่อด้านซ้าย เพื่อดูแฟ้มประวัติการรักษาทั้งหมดอย่างละเอียด</p>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php include '../includes/footer.php'; ?>