const revenueCtx = document.getElementById('revenueChart').getContext('2d');
let revenueChart;

let salesData = [];
let roomPaymentsData = [];

// ----------------- Fetch Data -----------------
async function fetchData(period='monthly', value='') {
    try {
        const params = new URLSearchParams();
        params.append('period', period);
        if(value) params.append('value', value);

        const response = await fetch('fetch_sales_revenue.php?' + params.toString());
        const data = await response.json();

        salesData = data.sales;
        roomPaymentsData = data.room_payments;

        renderRevenueChart(data.revenueData); // system chart
        renderSalesTable(salesData);
        renderRoomPaymentsTable(roomPaymentsData);

        populateSalesDropdowns();
        populateRoomDropdowns();

    } catch(err) {
        console.error('Error fetching data:', err);
    }
}

function renderRevenueChart(data){
    if(revenueChart) revenueChart.destroy();

    // Define different colors for each category
    const colors = {
        'Room Service': 'rgba(255, 99, 132, 1)',   // Red
        'Restaurant': 'rgba(54, 162, 235, 1)',     // Blue
        'Mini Bar': 'rgba(255, 205, 86, 1)',       // Yellow
        'Gift Store': 'rgba(75, 192, 192, 1)',     // Teal
        'Lounge Bar': 'rgba(153, 102, 255, 1)',    // Purple
        'Room Payments': 'rgba(255, 159, 64, 1)'   // Orange
    };

    // Prepare datasets for each category
    const datasets = [];
    const categories = ['Room Service', 'Restaurant', 'Mini Bar', 'Gift Store', 'Lounge Bar', 'Room Payments'];

    categories.forEach(category => {
        const categoryData = data.map(period => period.revenue[category] || 0);
        datasets.push({
            label: category,
            data: categoryData,
            borderColor: colors[category],
            backgroundColor: colors[category],
            fill: false,
            tension: 0.4,
            borderWidth: 3,
            pointBackgroundColor: colors[category],
            pointBorderColor: '#fff',
            pointRadius: 5,
            pointHoverRadius: 7,
            pointHoverBackgroundColor: colors[category],
            pointBorderWidth: 2
        });
    });

    // Prepare labels (periods)
    const labels = data.map(period => {
        const periodType = document.getElementById('periodSelect').value;
        const periodValue = document.getElementById(periodType === 'monthly' ? 'monthSelect' : periodType === 'weekly' ? 'weekSelect' : periodType === 'daily' ? 'daySelect' : 'yearSelect').value;

        if (periodValue) {
            // Specific period selected - detailed breakdown
            switch(periodType) {
                case 'daily':
                    // Hourly labels
                    const hour = period.period.split(' ')[1].split(':')[0];
                    return `${hour}:00`;
                case 'weekly':
                    // Daily labels for selected week
                    return new Date(period.period).toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
                case 'monthly':
                    // Daily labels for selected month (show day number)
                    const dayDate = new Date(period.period);
                    return dayDate.getDate().toString();
                case 'yearly':
                    // Monthly labels for selected year
                    const [year, month] = period.period.split('-');
                    return new Date(year, month - 1).toLocaleDateString('en-US', { month: 'short' });
                default:
                    return period.period;
            }
        } else {
            // Historical trends
            switch(periodType) {
                case 'monthly':
                    const [year, month] = period.period.split('-');
                    return new Date(year, month - 1).toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
                case 'weekly':
                    const weekNum = period.period.split('-W')[1];
                    return weekNum ? `Week ${weekNum}` : 'Week Undefined';
                case 'daily':
                    return new Date(period.period).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                case 'yearly':
                    return period.period;
                default:
                    return period.period;
            }
        }
    });

    revenueChart = new Chart(revenueCtx,{
        type:'line',
        data:{
            labels,
            datasets
        },
        options:{
            responsive:true,
            maintainAspectRatio:false,
            plugins:{
                legend:{
                    labels:{
                        color:'#fff',
                        font:{size:12, weight:'bold'},
                        usePointStyle: true,
                        padding: 20
                    },
                    position: 'bottom'
                },
                title:{
                    display:true,
                    text:[
                        'Hotel La Vista Revenue Trends',
                        'Total Revenue: ₱' + data.reduce((sum, period) => {
                            return sum + Object.values(period.revenue).reduce((periodSum, val) => periodSum + (val || 0), 0);
                        }, 0).toLocaleString()
                    ],
                    color:'#fff',
                    font:{size:18,weight:'bold'},
                    padding:{top:10,bottom:30}
                },
                tooltip:{
                    backgroundColor:'rgba(0,0,0,0.8)',
                    callbacks:{
                        label: ctx => `${ctx.dataset.label}: ₱${ctx.formattedValue}`
                    }
                }
            },
            scales:{
                x:{
                    ticks:{color:'#fff', font:{weight:'bold'}},
                    grid:{color:'rgba(255,255,255,0.1)'}
                },
                y:{
                    beginAtZero:true,
                    ticks:{
                        color:'#fff',
                        callback: value => '₱' + value.toLocaleString()
                    },
                    grid:{color:'rgba(255,255,255,0.1)'}
                }
            },
            interaction: {
                mode: 'index',
                intersect: false
            }
        }
    });
}

// ----------------- Sales Table -----------------
function renderSalesTable(sales){
    const tbody = document.getElementById('salesTableBody'); 
    tbody.innerHTML='';
    if(!sales || sales.length===0){ 
        tbody.innerHTML=`<tr><td colspan="8" style="text-align:center;padding:15px;">No sales data available.</td></tr>`; 
        return; 
    }
    sales.forEach((s,i)=>{
        const row=document.createElement('tr');
        row.innerHTML=`<td>${i+1}</td>
            <td>${s.guest_name}</td>
            <td>${s.order_type}</td>
            <td>${s.item}</td>
            <td style="text-align:right;">₱${parseFloat(s.total_amount).toFixed(2)}</td>
            <td>${s.payment_option}</td>
            <td>${s.payment_method}</td>
            <td>${new Date(s.created_at).toLocaleDateString()}</td>`;
        tbody.appendChild(row);
    });
}

// ----------------- Room Payments Table -----------------
function renderRoomPaymentsTable(payments){
    const tbody = document.getElementById('roomPaymentsBody'); 
    tbody.innerHTML='';
    if(!payments || payments.length===0){
        tbody.innerHTML=`<tr><td colspan="10" style="text-align:center;padding:15px;">No room payment data available.</td></tr>`; 
        return; 
    }
    payments.forEach((p,i)=>{
        const total = (parseFloat(p.room_price)||0)+(parseFloat(p.extended_price)||0);
        const row = document.createElement('tr');
        const bookingType = p.reservation_id ? 'Reservation' : (p.walkin_id ? 'Walk-in' : 'Unknown');
        row.innerHTML=`<td>${i+1}</td>
            <td>${p.guest_name}</td>
            <td>${bookingType}</td>
            <td>${p.room_type}</td>
            <td style="text-align:right;">₱${parseFloat(p.room_price).toFixed(2)}</td>
            <td>${p.stay||'-'}</td>
            <td>${p.extended_duration||'-'}</td>
            <td style="text-align:right;">₱${parseFloat(p.extended_price).toFixed(2)}</td>
            <td style="text-align:right;">₱${total.toFixed(2)}</td>
            <td>${new Date(p.created_at).toLocaleDateString()}</td>`;
        tbody.appendChild(row);
    });
}

// ----------------- Dropdowns -----------------
function populateSalesDropdowns(){
    const orderTypeSet = new Set(salesData.map(s=>s.order_type).filter(Boolean));
    document.getElementById('salesOrderType').innerHTML = `<option value="">All Order Type</option>` + [...orderTypeSet].map(v=>`<option value="${v}">${v}</option>`).join('');

    const paymentOptions = ['Paid','To be billed','Refunded','Partial Payment'];
    document.getElementById('salesPaymentOption').innerHTML = `<option value="">All Payment Option</option>` + paymentOptions.map(v=>`<option value="${v}">${v}</option>`).join('');

    const paymentMethods = ['Cash','Card','GCash','Paymaya','BillEase'];
    document.getElementById('salesPaymentMethod').innerHTML = `<option value="">All Payment Method</option>` + paymentMethods.map(v=>`<option value="${v}">${v}</option>`).join('');
}

function populateRoomDropdowns(){
    const bookingSet = new Set(roomPaymentsData.map(r=>r.reservation_id ? 'Reservation' : (r.walkin_id ? 'Walk-in' : 'Unknown')));
    document.getElementById('roomBookingType').innerHTML = `<option value="">All Booking Type</option>` + [...bookingSet].map(v=>`<option value="${v}">${v}</option>`).join('');

    const roomSet = new Set(roomPaymentsData.map(r=>r.room_type).filter(Boolean));
    document.getElementById('roomTypeFilter').innerHTML = `<option value="">All Room Type</option>` + [...roomSet].map(v=>`<option value="${v}">${v}</option>`).join('');
}

// ----------------- Filters -----------------
function applySalesFilters(){
    let filtered = salesData;
    const guest = document.getElementById('salesGuestSearch').value.toLowerCase();
    const orderType = document.getElementById('salesOrderType').value;
    const paymentOption = document.getElementById('salesPaymentOption').value;
    const paymentMethod = document.getElementById('salesPaymentMethod').value;

    filtered = filtered.filter(s=>{
        return (!guest || (s.guest_name||'').toLowerCase().includes(guest)) &&
               (!orderType || s.order_type === orderType) &&
               (!paymentOption || s.payment_option === paymentOption) &&
               (!paymentMethod || s.payment_method === paymentMethod);
    });
    renderSalesTable(filtered);
}

function applyRoomFilters(){
    let filtered = roomPaymentsData;
    const guest = document.getElementById('roomGuestSearch').value.toLowerCase();
    const bookingType = document.getElementById('roomBookingType').value;
    const roomType = document.getElementById('roomTypeFilter').value;

    filtered = filtered.filter(r=>{
        const bType = r.reservation_id ? 'Reservation' : (r.walkin_id ? 'Walk-in' : 'Unknown');
        return (!guest || (r.guest_name||'').toLowerCase().includes(guest)) &&
               (!bookingType || bType === bookingType) &&
               (!roomType || r.room_type === roomType);
    });
    renderRoomPaymentsTable(filtered);
}

// ----------------- Event Listeners -----------------
document.querySelectorAll('#salesGuestSearch, #salesOrderType, #salesPaymentOption, #salesPaymentMethod')
    .forEach(el=> el.addEventListener('input', applySalesFilters));

document.querySelectorAll('#roomGuestSearch, #roomBookingType, #roomTypeFilter')
    .forEach(el=> el.addEventListener('input', applyRoomFilters));

// Tabs
document.querySelectorAll('.tab-btn').forEach(btn=>{
    btn.addEventListener('click', ()=>{
        document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(t=>t.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById(btn.dataset.tab).classList.add('active');
    });
});

// Period filter change
document.getElementById('periodSelect').addEventListener('change', function() {
    const period = this.value;
    document.getElementById('monthLabel').style.display = period === 'monthly' ? 'inline' : 'none';
    document.getElementById('weekLabel').style.display = period === 'weekly' ? 'inline' : 'none';
    document.getElementById('dayLabel').style.display = period === 'daily' ? 'inline' : 'none';
    document.getElementById('yearLabel').style.display = period === 'yearly' ? 'inline' : 'none';

    // Set default values and fetch data
    const now = new Date();
    let value = '';
    switch(period) {
        case 'monthly':
            value = now.toISOString().slice(0,7);
            document.getElementById('monthSelect').value = value;
            break;
        case 'weekly':
            const weekStart = new Date(now.setDate(now.getDate() - now.getDay()));
            value = weekStart.toISOString().slice(0,10);
            document.getElementById('weekSelect').value = value;
            break;
        case 'daily':
            value = now.toISOString().slice(0,10);
            document.getElementById('daySelect').value = value;
            break;
        case 'yearly':
            value = now.getFullYear().toString();
            document.getElementById('yearSelect').value = value;
            break;
    }
    fetchData(period, value);
});

// Individual filter changes
document.getElementById('monthSelect').addEventListener('change', ()=>{
    fetchData('monthly', document.getElementById('monthSelect').value);
});

document.getElementById('weekSelect').addEventListener('change', ()=>{
    fetchData('weekly', document.getElementById('weekSelect').value);
});

document.getElementById('daySelect').addEventListener('change', ()=>{
    fetchData('daily', document.getElementById('daySelect').value);
});

document.getElementById('yearSelect').addEventListener('change', ()=>{
    fetchData('yearly', document.getElementById('yearSelect').value);
});

// Default to yearly
document.getElementById('periodSelect').value = 'yearly';
document.getElementById('monthLabel').style.display = 'none';
document.getElementById('weekLabel').style.display = 'none';
document.getElementById('dayLabel').style.display = 'none';
document.getElementById('yearLabel').style.display = 'inline';

const currentYear = new Date().getFullYear().toString();
document.getElementById('yearSelect').value = currentYear;
fetchData('yearly', currentYear);

// ----------------- Export PDF -----------------
async function exportToPDF() {
    try {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({ orientation:'landscape', unit:'mm', format:'a4', putOnlyUsedFonts:true, floatPrecision:16 });
        const margin = 15;
        const pageWidth = doc.internal.pageSize.getWidth();
        const pageHeight = doc.internal.pageSize.getHeight();

        // ----------------- Period Range Calculation -----------------
        const selectedPeriod = document.getElementById('periodSelect').value;
        let periodName = '';
        switch(selectedPeriod) {
            case 'monthly':
                const selectedMonth = document.getElementById('monthSelect').value;
                if(selectedMonth){
                    const startDate = new Date(selectedMonth + '-01');
                    const endDate = new Date(startDate.getFullYear(), startDate.getMonth()+1, 0);
                    periodName = `${startDate.toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' })} to ${endDate.toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' })}`;
                } else {
                    const today = new Date();
                    periodName = `${today.toLocaleDateString('en-US')} to ${today.toLocaleDateString('en-US')}`;
                }
                break;

            case 'weekly':
                const selectedWeek = document.getElementById('weekSelect').value;
                if(selectedWeek){
                    const [year, week] = selectedWeek.split('-W');
                    const weekStart = new Date(year, 0, 1 + (week - 1) * 7);
                    const dayOfWeek = weekStart.getDay();
                    const monday = new Date(weekStart); monday.setDate(weekStart.getDate() - dayOfWeek + 1);
                    const sunday = new Date(monday); sunday.setDate(monday.getDate() + 6);
                    periodName = `${monday.toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' })} to ${sunday.toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' })}`;
                } else {
                    const today = new Date();
                    periodName = today.toLocaleDateString('en-US');
                }
                break;

            case 'daily':
                const selectedDay = document.getElementById('daySelect').value;
                if(selectedDay){
                    const day = new Date(selectedDay);
                    periodName = day.toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' });
                } else {
                    const today = new Date();
                    periodName = today.toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' });
                }
                break;

            case 'yearly':
                const selectedYear = document.getElementById('yearSelect').value;
                if(selectedYear){
                    periodName = `Jan 1, ${selectedYear} to Dec 31, ${selectedYear}`;
                } else {
                    const year = new Date().getFullYear();
                    periodName = `Jan 1, ${year} to Dec 31, ${year}`;
                }
                break;
        }

        // ----------------- Title & Description -----------------
        doc.setFont("times","bold"); 
        doc.setFontSize(24);
        doc.text("Sales & Revenue Report - Hotel La Vista", margin, margin+10);

        doc.setFont("times","bold");
        doc.setFontSize(16);
        doc.text(`Report for: ${periodName}`, margin, margin+25);

        doc.setFont("times","normal"); 
        doc.setFontSize(14);
        doc.text("Generated on: "+new Date().toLocaleString(), margin, margin+35);

        let y = margin + 45;
        const description = [
            "This report provides an overview of sales and revenue at Hotel La Vista for the selected period.",
            "It includes a breakdown of sales transactions, room payments, and revenue per service category."
        ];
        doc.setFontSize(12);
        description.forEach(line=>{ doc.text(line, margin, y); y+=7; });

        // ----------------- Room Type Details Table -----------------
        y += 10;
        doc.setFont("times","bold"); doc.setFontSize(16);
        doc.text("Room Type Details", margin, y);
        y += 8;
        doc.setFont("times","normal"); doc.setFontSize(12);

        const roomTypes = ['Single Room','Double Room','Twin Room','Deluxe Room','Suite','Family Room','VIP Room'];
        const occupancy = roomTypes.map(rt=>roomPaymentsData.filter(r=>r.room_type===rt).length);
        const revenue = roomTypes.map(rt=>roomPaymentsData.filter(r=>r.room_type===rt).reduce((sum,r)=>sum+(parseFloat(r.room_price)||0)+(parseFloat(r.extended_price)||0),0));

        const rowHeight=8;
        let yPos = y;
        doc.setFont("times","bold"); doc.setFontSize(14);
        doc.text("Room Type", margin, yPos);
        doc.text("Occupancy", margin+70, yPos);
        doc.text("Revenue (PHP)", margin+140, yPos);
        doc.setFont("times","normal");
        yPos += rowHeight;

        roomTypes.forEach((type,i)=>{
            if(yPos>pageHeight-margin){ doc.addPage(); yPos=margin; }
            doc.text(type, margin, yPos);
            doc.text(occupancy[i].toString(), margin+70, yPos);
            doc.text(revenue[i].toLocaleString(), margin+140, yPos);
            yPos += rowHeight;
        });

        // ----------------- Bar Chart on New Page -----------------
        doc.addPage();
        yPos = margin;
        doc.setFont("times","bold"); doc.setFontSize(18);
        doc.text("Revenue Bar Chart", margin, yPos);
        yPos += 8;
        doc.setFont("times","normal"); doc.setFontSize(14);
        doc.text("Bar chart showing revenue by service category", margin, yPos);
        yPos += 12;

        const chartLabels = ['Room Service', 'Restaurant', 'Mini Bar', 'Gift Store', 'Lounge', 'Room Payments'];
        const chartData = [];
        const revenueByType = {};
        salesData.forEach(sale => {
            if (!revenueByType[sale.order_type]) revenueByType[sale.order_type] = 0;
            revenueByType[sale.order_type] += parseFloat(sale.total_amount) || 0;
        });
        const roomPaymentsTotal = roomPaymentsData.reduce((sum, payment) => {
            return sum + (parseFloat(payment.room_price) || 0) + (parseFloat(payment.extended_price) || 0);
        }, 0);
        revenueByType['Room Payments'] = roomPaymentsTotal;

        chartLabels.forEach(label => {
            chartData.push({ label: label, value: revenueByType[label] || 0 });
        });

        if(chartData.length > 0){
            const maxValue = Math.max(...chartData.map(d => d.value)) || 1;
            const chartWidth = pageWidth - margin*2;
            const chartHeight = pageHeight - margin*3 - 30;
            const barWidth = Math.max(chartWidth / chartData.length * 0.8, 10);
            const barSpacing = Math.max(chartWidth / chartData.length * 0.2, 5);

            chartData.forEach((data, index)=>{
                const barHeight = Math.max((data.value / maxValue) * chartHeight, 5);
                const x = margin + index*(barWidth+barSpacing);
                const y = yPos + chartHeight - barHeight;

                doc.setFillColor(0,0,128); // Blue bars
                doc.rect(x, y, barWidth, barHeight, 'F');

                // Value on top
                doc.setFontSize(12);
                doc.setTextColor(255,255,255);
                if(barHeight>12) doc.text(data.value.toLocaleString(), x + barWidth/2 - 10, y + barHeight/2 + 2);

                // Label below
                doc.setFontSize(10);
                doc.setTextColor(0,0,0);
                const labelText = data.label.length>15 ? data.label.substring(0,15)+'...' : data.label;
                doc.text(labelText, x + barWidth/2 - 15, yPos + chartHeight + 12);
            });
        }

        // ----------------- Sales Details Table -----------------
        doc.addPage();
        yPos = margin;
        doc.setFont("times","bold"); doc.setFontSize(16);
        doc.text("Sales Details Table", margin, yPos);
        yPos += 8;
        doc.setFont("times","normal"); doc.setFontSize(12);
        doc.text("Complete list of all sales transactions with guest information and payment details", margin, yPos);
        yPos += 10;

        const salesHeaders = ["#","Guest Name","Order Type","Item","Amount","Payment Option","Payment Method","Date"];
        doc.setFont("times","bold"); doc.setFontSize(12);
        let xPos = margin;
        salesHeaders.forEach(header=>{ doc.text(header,xPos,yPos); xPos+=30; });
        yPos += 8;
        doc.setFont("times","normal");

        salesData.forEach((s,index)=>{
            if(yPos>pageHeight-margin){ doc.addPage(); yPos=margin; xPos=margin; doc.setFont("times","bold"); salesHeaders.forEach(h=>{ doc.text(h,xPos,yPos); xPos+=30; }); yPos+=8; doc.setFont("times","normal"); }
            xPos = margin;
            const row = [(index+1).toString(), s.guest_name||'N/A', s.order_type||'N/A', s.item||'N/A', 'PHP '+parseFloat(s.total_amount).toFixed(2), s.payment_option||'N/A', s.payment_method||'N/A', new Date(s.created_at).toLocaleDateString()];
            row.forEach(d=>{ doc.text(d.toString().slice(0,20), xPos, yPos); xPos+=30; });
            yPos += rowHeight;
        });

        // ----------------- Room Payments Table -----------------
        doc.addPage();
        yPos = margin;
        doc.setFont("times","bold"); doc.setFontSize(16);
        doc.text("Room Payments Details Table", margin, yPos);
        yPos += 8;
        doc.setFont("times","normal"); doc.setFontSize(12);
        doc.text("Complete list of all room payment transactions including booking details and pricing", margin, yPos);
        yPos += 10;

        const roomHeaders = ["#","Guest Name","Booking Type","Room Type","Room Price","Stay","Extended","Ext Price","Total","Date"];
        doc.setFont("times","bold"); doc.setFontSize(11);
        xPos = margin;
        roomHeaders.forEach(header=>{ doc.text(header,xPos,yPos); xPos+=25; });
        yPos += 8;
        doc.setFont("times","normal");

        roomPaymentsData.forEach((p,index)=>{
            if(yPos>pageHeight-margin){ doc.addPage(); yPos=margin; xPos=margin; doc.setFont("times","bold"); roomHeaders.forEach(h=>{ doc.text(h,xPos,yPos); xPos+=25 }); yPos+=8; doc.setFont("times","normal"); }
            xPos = margin;
            const bookingType = p.reservation_id?'Reservation':(p.walkin_id?'Walk-in':'Unknown');
            const total = (parseFloat(p.room_price)||0)+(parseFloat(p.extended_price)||0);
            const row = [(index+1).toString(), p.guest_name||'N/A', bookingType, p.room_type||'N/A', 'PHP '+parseFloat(p.room_price).toFixed(2), p.stay||'-', p.extended_duration||'-', 'PHP '+parseFloat(p.extended_price).toFixed(2), 'PHP '+total.toFixed(2), new Date(p.created_at).toLocaleDateString()];
            row.forEach(d=>{ doc.text(d.toString().slice(0,20), xPos, yPos); xPos+=25 });
            yPos += rowHeight;
        });

        // ----------------- Save PDF -----------------
        const fileName = `sales_revenue_report_${selectedPeriod}_${selectedPeriod === 'monthly' ? (document.getElementById('monthSelect').value || 'current') : selectedPeriod === 'weekly' ? (document.getElementById('weekSelect').value || 'current') : selectedPeriod === 'daily' ? (document.getElementById('daySelect').value || 'current') : (document.getElementById('yearSelect').value || 'current')}.pdf`;
        setTimeout(()=>{ doc.save(fileName); },500);

    } catch (error) {
        console.error('Error generating PDF:', error);
        alert('Error generating PDF: ' + error.message);
    }
    const selectedPeriod = document.getElementById('periodSelect').value;
    const fileName = `sales_revenue_report_${selectedPeriod}_${selectedPeriod === 'monthly' ? (document.getElementById('monthSelect').value || 'current') : selectedPeriod === 'weekly' ? (document.getElementById('weekSelect').value || 'current') : selectedPeriod === 'daily' ? (document.getElementById('daySelect').value || 'current') : (document.getElementById('yearSelect').value || 'current')}.csv`;
    const blob = new Blob([csv], { type:'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a'); a.href=url; a.download=fileName; a.click();
    window.URL.revokeObjectURL(url);
}
