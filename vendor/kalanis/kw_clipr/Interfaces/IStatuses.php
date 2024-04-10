<?php

namespace kalanis\kw_clipr\Interfaces;


/**
 * Interface IStatuses
 * @package kalanis\kw_clipr\Interfaces
 * Available response statuses
 * @link https://shapeshed.com/unix-exit-codes/
 * @link https://unix.stackexchange.com/questions/110348/how-do-i-get-the-list-of-exit-codes-and-or-return-codes-and-meaning-for-a-comm
 * @link https://tldp.org/LDP/abs/html/exitcodes.html
 * @see BSD man sysexits
 * @link https://man.openbsd.org/sysexits
 * @see man signal
 * @link https://www.man7.org/linux/man-pages/man7/signal.7.html
 */
interface IStatuses
{
    public const STATUS_SUCCESS = 0; // you usually need mainly this - task ends successfully
    public const STATUS_ERROR = 1; // something went wrong inside the app - the second most used signal
    public const STATUS_LIB_ERROR = 2; // something died within library (Dependency Injection error? Unknown class or other resource?)
    public const STATUS_NO_TARGET_RESOURCE = 6; // cannot find target file/device/other resource
    // sysexits
    public const STATUS_CLI_USAGE = 64; // task used incorrectly (like wrong number of arguments, bad syntax, ...)
    public const STATUS_INPUT_ERROR = 65; // content of input arguments is incorrect and probably unusable
    public const STATUS_NO_INPUT_FILE = 66; // common input file which shall be processed does not exist or is not readable
    public const STATUS_NO_AVAILABLE_USER = 67; // passed user does not exist (usually for remote tasks with different user)
    public const STATUS_NO_AVAILABLE_HOST = 68; // passed host does not exist (usually for remote tasks with specified host)
    public const STATUS_NO_AVAILABLE_SERVICE = 69; // necessary supporting service is not available
    public const STATUS_SW_INTERNAL = 70; // some error happens during processing the code (like syntax error or logic error)
    public const STATUS_OS_OR_PKG_ERROR = 71; // problems with OS or necessary third party executables
    public const STATUS_OS_OR_PKG_FILE_MISS = 72; // problems with system files (like /etc/passwd)
    public const STATUS_CANNOT_CREATE = 73; // common output file which shall be used does not exists or is not writable
    public const STATUS_FILE_IO_ERROR = 74; // problems with IO operations on affected file
    public const STATUS_ATTEMPT_LATER = 75; // temporary failure (like fallen network or existing lock), worth to try to run it later
    public const STATUS_REMOTE_COMM_MISS = 76; // your task and remote server cannot understand each other (in query you sent HTML form, the service want JSON)
    public const STATUS_PERMISSION_BLOCK = 77; // the task cannot access the resource due insufficient permissions (either local or remote)
    public const STATUS_BAD_CONFIG = 78; // bogus in configuration; must be changed manually
    // subtasks problems
    public const STATUS_TIMEOUT = 124; // subtask is out of available time
    public const STATUS_SOMETHING_FAILS = 125;
    public const STATUS_CANNOT_EXECUTE = 126; // cannot execute task (usually Clipr one)
    public const STATUS_NOT_FOUND = 127; // command called by Clipr not found
    public const STATUS_INVALID_RESULT = 128; // you got invalid code to response (like 3.141592 or "string")
    // with signals - 128+N
    public const STATUS_SIGNAL_RESTART = 129; // SIGHUP, restart task
    public const STATUS_SIGNAL_INTERRUPT = 130; // SIGINT, came Control-C
    public const STATUS_SIGNAL_QUIT = 131; // SIGQUIT
    public const STATUS_SIGNAL_ILLEGAL_INSTRUCTION = 132; // SIGILL, illegal instruction; can be used when you use php library which is not set on server (like BC)
    public const STATUS_SIGNAL_DUMP = 133; // SIGTRAP, trace/breakpoint dump; usable with thrown CliprException('your dump', IStatuses::STATUS_SIGNAL_DUMP)
    public const STATUS_SIGNAL_ABORT = 134; // SIGABRT, abort previous signal
    public const STATUS_SIGNAL_BUS_ERROR = 135; // SIGBUS, when you have problems with memory (bad cloned objects?)
    public const STATUS_SIGNAL_FLOATING_POINT = 136; // SIGFPE, floating point fail (like calling pow(1.2345, -6.789) )
    public const STATUS_SIGNAL_KILL = 137; // SIGKILL, kill task immediately, cannot be stopped
    public const STATUS_SIGNAL_USER_1 = 138; // SIGUSR1, your user wants something
    public const STATUS_SIGNAL_MEM_REFERENCE = 139; // SIGSEGV, invalid reference in memory (Segmentation Fault - try to access uninicialized object?)
    public const STATUS_SIGNAL_USER_2 = 140; // SIGUSR2, your user wants something else
    public const STATUS_SIGNAL_PIPE = 141; // SIGPIPE, piped result cannot continue (output sent to pipe just leaking to the black hole)
    public const STATUS_SIGNAL_ALARM = 142; // SIGALRM, timer just calls for your attention
    public const STATUS_SIGNAL_TERMINATE = 143; // SIGTERM, gracefully end task
    public const STATUS_SIGNAL_STACK_FAULT = 144; // SIGSTKFLT, stack fault, usually unused
    public const STATUS_SIGNAL_CHILD_STOPPED = 145; // SIGCHLD, expected child stopped or terminated
    public const STATUS_SIGNAL_CONTINUE = 146; // SIGCONT, continue stopped process (after things like ALARM or STOP)
    public const STATUS_SIGNAL_STOP = 147; // SIGSTOP, stop task immediately, cannot be stopped on the way - caution!
    public const STATUS_SIGNAL_STOP_TERM = 148; // SIGSTSTP, stop from terminal, came Control-Z
    public const STATUS_SIGNAL_TERMINAL_INPUT = 149; // SIGTTIN, input from terminal for background task
    public const STATUS_SIGNAL_TERMINAL_OUTPUT = 150; // SIGTTOU, output to terminal for background task
    public const STATUS_SIGNAL_URGENT_CONDITION = 151; // SIGURG, socket/resource wants attention
    public const STATUS_SIGNAL_CPU_TIMER_TIMEOUT = 152; // SIGXCPU
    public const STATUS_SIGNAL_FILE_SIZE_TOO_LARGE = 153; // SIGXFSZ
    public const STATUS_SIGNAL_VIRTUAL_CLOCK_ALARM = 154; // SIGVTALRM
    public const STATUS_SIGNAL_PROFILING_TIMEOUT = 155; // SIGPROF
    public const STATUS_SIGNAL_WINDOW_RESIZE = 156; // SIGWINCH
    public const STATUS_SIGNAL_IO_FAILS = 157; // SIGIO, IO operation is not possible now
    public const STATUS_SIGNAL_POWER_FAILURE = 158; // SIGPWR
    public const STATUS_SIGNAL_BAD_SYS_CALL = 159; // SIGSYS
    // rest
    public const STATUS_NO_PROCESS_ID = 249; // child process has no ID
    public const STATUS_OUT_OF_RANGE = 255; // you got code out of range (0-255) as response from task; reserved for PHP
}
