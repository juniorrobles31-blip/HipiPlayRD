import 'dotenv/config';
import { ethers } from 'ethers';

const ABI = [
  'function commitState(bytes32 stateHash, bytes32 previousHash, string entityType, uint256 entityId, uint256 eventId, string metadataUri) external'
];

const required = ['RPC_URL', 'PRIVATE_KEY', 'CONTRACT_ADDRESS', 'SYSTEM_URL'];
for (const key of required) {
  if (!process.env[key]) throw new Error(`Falta variable ${key} en .env`);
}

const provider = new ethers.JsonRpcProvider(process.env.RPC_URL);
const wallet = new ethers.Wallet(process.env.PRIVATE_KEY, provider);
const contract = new ethers.Contract(process.env.CONTRACT_ADDRESS, ABI, wallet);
const limit = process.env.AUDIT_LIMIT || 10;

async function post(service, data = {}) {
  const body = new URLSearchParams({ service, ...data });
  const res = await fetch(process.env.SYSTEM_URL, { method: 'POST', body });
  const json = await res.json();
  if (!res.ok || json.STATUS === 'ERROR') throw new Error(json.INFO || `HTTP ${res.status}`);
  return json;
}

async function main() {
  const pending = await post('horse.audit.pending', { limit });
  const rows = pending.audits || [];
  console.log(`Auditorias pendientes: ${rows.length}`);

  for (const row of rows) {
    console.log(`Enviando audit #${row.id_audit}: ${row.state_hash}`);
    try {
      const tx = await contract.commitState(
        row.state_hash,
        row.previous_hash,
        row.entity_type,
        BigInt(row.entity_id || 0),
        BigInt(row.id_event || 0),
        ''
      );
      await post('horse.audit.mark_tx', {
        id_audit: row.id_audit,
        tx_hash: tx.hash,
        status: 'submitted'
      });
      console.log(`TX enviada: ${tx.hash}`);
      const receipt = await tx.wait(1);
      await post('horse.audit.mark_tx', {
        id_audit: row.id_audit,
        tx_hash: tx.hash,
        status: receipt.status === 1 ? 'confirmed' : 'error'
      });
      console.log(`Confirmada: ${tx.hash}`);
    } catch (err) {
      console.error(`Error audit #${row.id_audit}:`, err.message);
      await post('horse.audit.mark_tx', {
        id_audit: row.id_audit,
        tx_hash: '',
        status: 'error',
        error: err.message
      }).catch(() => null);
    }
  }
}

main().catch(err => {
  console.error(err);
  process.exit(1);
});
