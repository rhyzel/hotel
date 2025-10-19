
document.getElementById('fetch_btn').addEventListener('click', async () => {
    const employeeId = document.getElementById('employee_id').value.trim();
    const messageEl = document.getElementById('salaryMessage');
    if (!employeeId) { messageEl.textContent="Please enter Employee ID"; messageEl.hidden=false; return; }

    try {
        const response = await fetch(`get_employee.php?staff_id=${employeeId}`);
        if (!response.ok) throw new Error('Network response not ok');
        const data = await response.json();
        if(data.status!=="success"){ messageEl.textContent=data.message; messageEl.hidden=false; return; }

        document.getElementById('employee_name').value = data.data.first_name+' '+data.data.last_name;
        document.getElementById('basic_salary').value = data.data.base_salary || 0;
        document.getElementById('allowances').value = data.data.allowances || 0;
        document.getElementById('overtime').value = data.data.overtime || 0;
        messageEl.hidden = true;
        calculateSalary();
    } catch(err){
        messageEl.textContent = "Error fetching employee: "+err.message;
        messageEl.hidden=false;
    }
});

function calculateSalary(){
    const basic=parseFloat(document.getElementById('basic_salary').value)||0;
    const allowances=parseFloat(document.getElementById('allowances').value)||0;
    const overtime=parseFloat(document.getElementById('overtime').value)||0;
    const gross = basic+allowances+overtime;
    const sss = gross*0.045;
    const philhealth = gross*0.0275;
    const pagibig = Math.min(100,gross*0.02);
    const withholding = gross>20000?(gross-20000)*0.2:0;
    const totalDeductions=sss+philhealth+pagibig+withholding;
    const net=gross-totalDeductions;

    document.getElementById('gross_pay').textContent="₱ "+gross.toFixed(2);
    document.getElementById('break_sss').textContent="₱ "+sss.toFixed(2);
    document.getElementById('break_phil').textContent="₱ "+philhealth.toFixed(2);
    document.getElementById('break_pagibig').textContent="₱ "+pagibig.toFixed(2);
    document.getElementById('break_withholding').textContent="₱ "+withholding.toFixed(2);
    document.getElementById('total_deductions').textContent="₱ "+totalDeductions.toFixed(2);
    document.getElementById('net_pay').textContent="₱ "+net.toFixed(2);
}

document.getElementById('calculate_btn').addEventListener('click',calculateSalary);

document.getElementById('save_btn').addEventListener('click', async () => {
    const employeeId=document.getElementById('employee_id').value.trim();
    if(!employeeId) return;
    const formData=new FormData();
    formData.append('employee_id',employeeId);
    formData.append('basic_salary',document.getElementById('basic_salary').value);
    formData.append('allowances',document.getElementById('allowances').value);
    formData.append('overtime',document.getElementById('overtime').value);
    formData.append('payroll_month',document.getElementById('payroll_month').value||'');
    formData.append('remarks',document.getElementById('remarks').value||'');

    try{
        const res=await fetch('salary_processing_backend.php',{method:'POST',body:formData});
        const data=await res.json();
        const messageEl=document.getElementById('salaryMessage');
        messageEl.textContent = data.message;
        messageEl.hidden=false;
    }catch(err){
        const messageEl=document.getElementById('salaryMessage');
        messageEl.textContent="Error saving salary: "+err.message;
        messageEl.hidden=false;
    }
});

