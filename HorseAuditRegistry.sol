// SPDX-License-Identifier: MIT
pragma solidity ^0.8.20;

/**
 * HorseAuditRegistry
 * Registro minimalista para anclar hashes de estado del Juego de Caballos.
 * No guarda datos personales ni montos detallados. Solo hashes y metadatos.
 */
contract HorseAuditRegistry {
    struct AuditCommit {
        bytes32 stateHash;
        bytes32 previousHash;
        string entityType;
        uint256 entityId;
        uint256 eventId;
        address committer;
        uint256 timestamp;
        string metadataUri;
    }

    mapping(bytes32 => bool) public committed;
    mapping(uint256 => AuditCommit) public commits;
    uint256 public totalCommits;

    event StateCommitted(
        bytes32 indexed stateHash,
        bytes32 indexed previousHash,
        string entityType,
        uint256 indexed eventId,
        uint256 entityId,
        address committer,
        uint256 timestamp,
        string metadataUri
    );

    function commitState(
        bytes32 stateHash,
        bytes32 previousHash,
        string calldata entityType,
        uint256 entityId,
        uint256 eventId,
        string calldata metadataUri
    ) external {
        require(stateHash != bytes32(0), "stateHash requerido");
        require(!committed[stateHash], "hash ya registrado");

        totalCommits += 1;
        committed[stateHash] = true;
        commits[totalCommits] = AuditCommit({
            stateHash: stateHash,
            previousHash: previousHash,
            entityType: entityType,
            entityId: entityId,
            eventId: eventId,
            committer: msg.sender,
            timestamp: block.timestamp,
            metadataUri: metadataUri
        });

        emit StateCommitted(
            stateHash,
            previousHash,
            entityType,
            eventId,
            entityId,
            msg.sender,
            block.timestamp,
            metadataUri
        );
    }
}
