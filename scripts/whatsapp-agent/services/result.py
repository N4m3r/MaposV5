"""Consistent result type for service operations."""
from dataclasses import dataclass
from typing import Any, Optional


@dataclass
class Result:
    """Standardized result object for service operations.

    Attributes:
        success: Whether the operation succeeded
        data: The result data (on success)
        error: Error message (on failure)
    """
    success: bool
    data: Any = None
    error: Optional[str] = None

    @staticmethod
    def ok(data=None) -> 'Result':
        return Result(success=True, data=data)

    @staticmethod
    def fail(error: str) -> 'Result':
        return Result(success=False, error=error)