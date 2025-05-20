// Asumsikan ini adalah data asli yang kamu miliki
const inputData = [
  {
    "record_date": "2025-05-14",
    "warehouse_name": "Warehouse A",
    "source_name": "CCTV-2",
    "shift_id": 1,
    "shift_name": "Pagi",
    "bag_type": "Granul",
    "total_quantity": 1
  },
  {
    "record_date": "2025-05-14",
    "warehouse_name": "Warehouse A",
    "source_name": "CCTV-1",
    "shift_id": 3,
    "shift_name": "Malam",
    "bag_type": "Bag",
    "total_quantity": 1
  },
  {
    "record_date": "2025-05-14",
    "warehouse_name": "Warehouse A",
    "source_name": "CCTV-1",
    "shift_id": 3,
    "shift_name": "Malam",
    "bag_type": "Prill",
    "total_quantity": 1
  },
  {
    "record_date": "2025-05-15",
    "warehouse_name": "Warehouse A",
    "source_name": "CCTV-2",
    "shift_id": 1,
    "shift_name": "Pagi",
    "bag_type": "Subsidi",
    "total_quantity": 1
  },
  {
    "record_date": "2025-05-15",
    "warehouse_name": "Warehouse A",
    "source_name": "CCTV-1",
    "shift_id": 3,
    "shift_name": "Malam",
    "bag_type": "Granul",
    "total_quantity": 1
  },
  {
    "record_date": "2025-05-16",
    "warehouse_name": "Warehouse A",
    "source_name": "CCTV-2",
    "shift_id": 1,
    "shift_name": "Pagi",
    "bag_type": "Bag",
    "total_quantity": 1
  },
  {
    "record_date": "2025-05-16",
    "warehouse_name": "Warehouse A",
    "source_name": "CCTV-2",
    "shift_id": 1,
    "shift_name": "Pagi",
    "bag_type": "Prill",
    "total_quantity": 1
  },
  {
    "record_date": "2025-05-16",
    "warehouse_name": "Warehouse A",
    "source_name": "CCTV-1",
    "shift_id": 3,
    "shift_name": "Malam",
    "bag_type": "Subsidi",
    "total_quantity": 1
  },
  {
    "record_date": "2025-05-17",
    "warehouse_name": "Warehouse A",
    "source_name": "CCTV-1",
    "shift_id": 2,
    "shift_name": "Siang",
    "bag_type": "Bag",
    "total_quantity": 1
  },
  {
    "record_date": "2025-05-17",
    "warehouse_name": "Warehouse A",
    "source_name": "CCTV-2",
    "shift_id": 3,
    "shift_name": "Malam",
    "bag_type": "Granul",
    "total_quantity": 1
  },
  {
    "record_date": "2025-05-17",
    "warehouse_name": "Warehouse A",
    "source_name": "CCTV-1",
    "shift_id": 3,
    "shift_name": "Malam",
    "bag_type": "Prill",
    "total_quantity": 1
  },
  {
    "record_date": "2025-05-18",
    "warehouse_name": "Warehouse A",
    "source_name": "CCTV-1",
    "shift_id": 2,
    "shift_name": "Siang",
    "bag_type": "Granul",
    "total_quantity": 1
  },
  {
    "record_date": "2025-05-18",
    "warehouse_name": "Warehouse A",
    "source_name": "CCTV-2",
    "shift_id": 3,
    "shift_name": "Malam",
    "bag_type": "Subsidi",
    "total_quantity": 1
  },
  {
    "record_date": "2025-05-19",
    "warehouse_name": "Warehouse A",
    "source_name": "CCTV-2",
    "shift_id": 2,
    "shift_name": "Siang",
    "bag_type": "Bag",
    "total_quantity": 1
  },
  {
    "record_date": "2025-05-19",
    "warehouse_name": "Warehouse A",
    "source_name": "CCTV-1",
    "shift_id": 2,
    "shift_name": "Siang",
    "bag_type": "Subsidi",
    "total_quantity": 1
  },
  {
    "record_date": "2025-05-19",
    "warehouse_name": "Warehouse A",
    "source_name": "CCTV-2",
    "shift_id": 3,
    "shift_name": "Malam",
    "bag_type": "Prill",
    "total_quantity": 1
  },
  {
    "record_date": "2025-05-20",
    "warehouse_name": "Warehouse A",
    "source_name": "CCTV-1",
    "shift_id": 1,
    "shift_name": "Pagi",
    "bag_type": "Bag",
    "total_quantity": 1
  },
  {
    "record_date": "2025-05-20",
    "warehouse_name": "Warehouse A",
    "source_name": "CCTV-1",
    "shift_id": 1,
    "shift_name": "Pagi",
    "bag_type": "Prill",
    "total_quantity": 1
  },
  {
    "record_date": "2025-05-20",
    "warehouse_name": "Warehouse A",
    "source_name": "CCTV-2",
    "shift_id": 2,
    "shift_name": "Siang",
    "bag_type": "Granul",
    "total_quantity": 1
  }
];

const result = [];

inputData.forEach(entry => {
  const { shift_name, source_name, bag_type, total_quantity } = entry;

  // Cari entri shift yang sesuai
  let shiftEntry = result.find(s => s.shift === shift_name);
  if (!shiftEntry) {
    shiftEntry = {
      shift: shift_name,
      "total bag": 0,
      subData: []
    };
    result.push(shiftEntry);
  }

  // Tambahkan jumlah ke total bag
  shiftEntry["total bag"] += total_quantity;

  // Cari entri CCTV di dalam shift
  let cctvEntry = shiftEntry.subData.find(s => s.name === source_name);
  if (!cctvEntry) {
    cctvEntry = { name: source_name, value: {} };
    shiftEntry.subData.push(cctvEntry);
  }

  // Tambahkan atau update jumlah tipe pupuk
  if (!cctvEntry.value[bag_type.toLowerCase()]) {
    cctvEntry.value[bag_type.toLowerCase()] = 0;
  }
  cctvEntry.value[bag_type.toLowerCase()] += total_quantity;
});

console.log(result);