const data = [
  {
    "warehouse_id": 1,
    "warehouse_name": "Warehouse A",
    "cctv_id": 1,
    "source_name": "CCTV-1",
    "ms_shift_id": 1,
    "shift_name": "Pagi",
    "total_bag": 918,
    "total_granul": 313,
    "total_subsidi": 608,
    "total_prill": 458,
    "start_time": "2025-04-23T00:00:00",
    "end_time": "2025-05-15T10:34:25"
  },
  {
    "warehouse_id": 1,
    "warehouse_name": "Warehouse A",
    "cctv_id": 1,
    "source_name": "CCTV-1",
    "ms_shift_id": 2,
    "shift_name": "Siang",
    "total_bag": 605,
    "total_granul": 205,
    "total_subsidi": 420,
    "total_prill": 295,
    "start_time": "2025-04-23T03:00:00",
    "end_time": "2025-04-23T04:00:00"
  },
  {
    "warehouse_id": 1,
    "warehouse_name": "Warehouse A",
    "cctv_id": 1,
    "source_name": "CCTV-1",
    "ms_shift_id": 3,
    "shift_name": "Malam",
    "total_bag": 580,
    "total_granul": 190,
    "total_subsidi": 420,
    "total_prill": 285,
    "start_time": "2025-04-23T05:00:00",
    "end_time": "2025-04-23T06:00:00"
  },
  {
    "warehouse_id": 1,
    "warehouse_name": "Warehouse A",
    "cctv_id": 2,
    "source_name": "CCTV-2",
    "ms_shift_id": 1,
    "shift_name": "Pagi",
    "total_bag": 510,
    "total_granul": 170,
    "total_subsidi": 330,
    "total_prill": 250,
    "start_time": "2025-04-23T00:00:00",
    "end_time": "2025-04-23T01:00:00"
  },
  {
    "warehouse_id": 1,
    "warehouse_name": "Warehouse A",
    "cctv_id": 2,
    "source_name": "CCTV-2",
    "ms_shift_id": 2,
    "shift_name": "Siang",
    "total_bag": 842,
    "total_granul": 332,
    "total_subsidi": 572,
    "total_prill": 452,
    "start_time": "2025-04-23T02:00:00",
    "end_time": "2025-05-15T10:34:25"
  },
  {
    "warehouse_id": 1,
    "warehouse_name": "Warehouse A",
    "cctv_id": 2,
    "source_name": "CCTV-2",
    "ms_shift_id": 3,
    "shift_name": "Malam",
    "total_bag": 910,
    "total_granul": 370,
    "total_subsidi": 530,
    "total_prill": 450,
    "start_time": "2025-04-23T05:00:00",
    "end_time": "2025-05-14T08:36:13"
  }
]

const uniqueShifts = [...new Set(data.map(item => item.shift_name))];

const result = [];

uniqueShifts.forEach(shift => {
  const shiftData = data.filter(d => d.shift_name === shift);
  const row = {
    shift: shift.toUpperCase()
  };

  shiftData.forEach(item => {
    const hours = getHours(item.start_time, item.end_time) || 1; // Hindari pembagian nol
    const prefix = `cctv_${item.cctv_id}`;
    row[`${prefix}_total_granul`] = +(item.total_granul / hours).toFixed(1);
    row[`${prefix}_total_prill`] = +(item.total_prill / hours).toFixed(1);
    row[`${prefix}_total_subsidi`] = +(item.total_subsidi / hours).toFixed(1);
    row[`${prefix}_total_bag`] = +(item.total_bag / hours).toFixed(1);
  });

  result.push(row);
});

console.log(result);