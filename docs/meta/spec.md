# Prophet — Package Specification

> **Cluster:** `logic`
> **Language:** `php`
> **Milestone:** `m6`
> **Repo:** `https://github.com/decodelabs/prophet`
> **Role:** AI assistants

## Overview

### Purpose

Prophet provides an opinionated set of interfaces for creating AI assistants that can be used across multiple platforms and services. It enables:

- Defining AI assistant blueprints with instructions and configuration
- Managing assistants across different AI platforms (OpenAI, etc.)
- Creating and managing conversation threads
- Handling message exchange between users and assistants
- Supporting multiple content mediums (text, code, JSON, image, speech, video, audio)
- Repository-based persistence for assistants, threads, and suggestions
- Generator pattern for AI-powered content generation
- Platform abstraction for different AI service providers
- Subject-based context management

Prophet provides a unified interface for building AI-powered applications, abstracting platform-specific details while maintaining flexibility.

### Non-Goals

- Prophet does not provide AI model implementations
- It does not handle HTTP communication with AI services (delegates to platform implementations)
- It does not provide a user interface or chat UI
- It does not handle authentication or API key management
- It does not provide rate limiting or quota management
- It does not handle cost tracking or billing
- It does not provide model training or fine-tuning

## Role in the Ecosystem

### Cluster & Positioning

Prophet belongs to the **logic** cluster, providing AI assistant abstractions and orchestration. It sits at the application layer, coordinating between platform-specific implementations and application-specific use cases.

### Usage Contexts

Prophet is used for:

- Building AI-powered chatbots and assistants
- Generating content via AI models
- Managing conversation threads and message history
- Implementing multi-platform AI integration
- Creating structured AI interactions with typed responses
- Building AI-assisted workflows and automation

## Public Surface

### Key Types

- **`Prophet`** — Main service class implementing Kingdom `Service`. Orchestrates blueprint loading, assistant/thread management, platform loading, and generation.

- **`Blueprint`** — Generic interface for defining AI assistant configuration. Includes action, name, instructions, medium, language model level, features, files, and functions.

- **`BlueprintTrait`** — Trait providing default implementations for Blueprint interface.

- **`Generator`** — Generic interface for AI-powered content generation. Extends Blueprint concept with typed output.

- **`Platform`** — Interface for AI service platform implementations. Defines methods for assistant/thread management, message fetching, and model suggestion.

- **`Repository`** — Generic interface for persisting assistants, threads, and suggestions. Provides CRUD operations for all entities.

- **`Subject`** — Interface for objects that can be subjects of AI assistance. Defines type and ID getters.

- **`Subject\Generic`** — Concrete implementation of Subject for generic subjects.

- **`Model\Assistant`** — Interface for AI assistant entities. Includes configuration, service ID, language model, timestamps, and metadata.

- **`Model\AssistantTrait`** — Trait providing default implementations for Assistant interface.

- **`Model\Thread`** — Interface for conversation thread entities. Includes subject, service ID, run ID, status, and timestamps.

- **`Model\ThreadTrait`** — Trait providing default implementations for Thread interface, including `isReady()` and `jsonSerialize()`.

- **`Model\Message`** — Class representing a message in a conversation. Contains ID, timestamp, role, and content items.

- **`Model\MessageList`** — Class representing a paginated list of messages. Includes pagination metadata.

- **`Model\Content`** — Interface for message content. Defines medium getter.

- **`Model\Content\Text`** — Concrete implementation for text/code content.

- **`Model\Content\Json`** — Concrete implementation for JSON content.

- **`Model\Content\File`** — Concrete implementation for file-based content (images, videos, audio).

- **`Model\Role`** — Enum defining message roles (Assistant, System, User).

- **`Model\RunStatus`** — Enum defining thread run statuses (Queued, InProgress, RequiresAction, Cancelling, Cancelled, Failed, Completed, Expired).

- **`Model\Suggestion`** — Interface for suggested follow-up options in a thread.

- **`Model\SuggestionTrait`** — Trait providing default implementations for Suggestion interface.

- **`Service\Medium`** — Enum defining content mediums (Text, Code, Json, Image, Speech, Video, Audio).

- **`Service\Feature`** — Enum defining platform features (Chat, Thread, CodeCompletion, Function, TextFile, PdfFile, ImageFile, VideoFile, AudioFile).

- **`Service\LanguageModelLevel`** — Enum defining model capability levels (Basic, Standard, Advanced).

### Main Entry Points

- **`Prophet::__construct(?Slingshot $slingshot = null)`** — Creates Prophet service instance. Creates new Slingshot if not provided.

- **`Prophet::loadPlatform(string $name): Platform`** — Loads a named Platform implementation via Slingshot.

- **`Prophet::getRepository(): Repository`** — Gets the repository instance. Resolves via Slingshot if not set.

- **`Prophet::loadBlueprint(string $name): Blueprint`** — Loads a named Blueprint implementation via Slingshot. Name is normalized to slug format.

- **`Prophet::loadGenerator(string $name): Generator`** — Loads a named Generator implementation. Tries Generator resolution first, then Blueprint resolution.

- **`Prophet::generate(string $name, Subject $subject): mixed`** — Generates content using a named generator for a subject.

- **`Prophet::tryLoadAssistant(string|Blueprint $blueprint, string $serviceName): ?Assistant`** — Loads an assistant from repository. Returns null if not found.

- **`Prophet::loadAssistant(string|Blueprint $blueprint, string $serviceName): Assistant`** — Loads or creates an assistant. Creates assistant in platform if needed. Updates language model if needed.

- **`Prophet::loadFreshAssistant(string|Blueprint $blueprint, string $serviceName): Assistant`** — Loads assistant and checks for model updates. Updates assistant if new model is available and should be updated.

- **`Prophet::updateAssistant(Assistant $assistant): void`** — Updates assistant in platform and repository. Throws Runtime exception on failure.

- **`Prophet::loadAndDeleteAssistant(string|Blueprint $blueprint, string $serviceName): bool`** — Loads and deletes an assistant. Returns false if not found.

- **`Prophet::deleteAssistant(Assistant $assistant): bool`** — Deletes assistant from platform and repository.

- **`Prophet::tryLoadThread(string|Blueprint $blueprint, Subject $subject): ?Thread`** — Loads a thread from repository. Returns null if not found.

- **`Prophet::loadThread(string|Blueprint $blueprint, Subject $subject): Thread`** — Loads or creates a thread. Starts thread in platform if needed. Refreshes thread if not ready.

- **`Prophet::loadAndPollThread(string|Blueprint $blueprint, Subject $subject): Thread`** — Loads thread and polls until ready.

- **`Prophet::pollThread(Thread $thread, int $attempts = 5): Thread`** — Polls thread status until ready or max attempts reached. Sleeps 3 seconds between attempts. Throws Runtime exception if not ready after max attempts.

- **`Prophet::refreshThread(Thread $thread): void`** — Refreshes thread status from platform. Throws Runtime exception if thread not initialized.

- **`Prophet::loadAndDeleteThread(string|Blueprint $blueprint, Subject $subject): bool`** — Loads and deletes a thread. Returns false if not found.

- **`Prophet::deleteThread(Thread $thread): bool`** — Deletes thread from platform and repository.

- **`Prophet::serializeThreadWithMessages(Thread $thread, int $limit = 20, ?string $afterId = null): array`** — Serializes thread and messages to JSON-compatible array.

- **`Prophet::fetchMessages(Thread $thread, int $limit = 20, ?string $afterId = null): MessageList`** — Fetches messages from platform. Returns empty list if thread not initialized.

- **`Prophet::reply(Thread $thread, string $message): Message`** — Sends a message to the thread and returns the response. Throws Runtime exception if thread not initialized.

- **`Blueprint::getAction(): string`** — Returns action identifier (slug format).

- **`Blueprint::getName(): string`** — Returns human-readable name.

- **`Blueprint::getInstructions(): string`** — Returns system instructions for the assistant.

- **`Blueprint::getMedium(): Medium`** — Returns primary content medium.

- **`Blueprint::getLanguageModelLevel(): LanguageModelLevel`** — Returns required model capability level.

- **`Blueprint::getFeatures(): array`** — Returns required platform features.

- **`Blueprint::getFiles(): array`** — Returns files to attach to the assistant.

- **`Blueprint::getFunctions(): array`** — Returns functions available to the assistant.

- **`Blueprint::generateAdditionalInstructions(Subject $subject): ?string`** — Generates subject-specific instructions.

- **`Generator::getAction(): string`** — Returns generator action identifier.

- **`Generator::generate(Subject $subject): mixed`** — Generates content for a subject.

- **`Platform::getName(): string`** — Returns platform name.

- **`Platform::supportsMedium(Medium $medium): bool`** — Checks if platform supports a medium.

- **`Platform::supportsFeature(Medium $medium, Feature $feature): bool`** — Checks if platform supports a feature for a medium.

- **`Platform::suggestModel(Medium $medium, LanguageModelLevel $level, array $features): string`** — Suggests appropriate model name for requirements.

- **`Platform::shouldUpdateModel(string $oldModel, string $newModel, Medium $medium, LanguageModelLevel $level, array $features): bool`** — Determines if assistant should be updated to new model.

- **`Platform::findAssistant(Assistant $assistant): bool`** — Searches for existing assistant by name in platform. Updates assistant with service ID if found.

- **`Platform::createAssistant(Assistant $assistant): void`** — Creates assistant in platform. Updates assistant with service ID.

- **`Platform::updateAssistant(Assistant $assistant): bool`** — Updates assistant in platform. Returns true if successful.

- **`Platform::deleteAssistant(Assistant $assistant): bool`** — Deletes assistant from platform. Returns true if successful.

- **`Platform::startThread(Assistant $assistant, Thread $thread, ?string $additionalInstructions): void`** — Starts a new thread in platform. Updates thread with service ID and run ID.

- **`Platform::refreshThread(Thread $thread): void`** — Refreshes thread status from platform. Updates thread status and completion time.

- **`Platform::deleteThread(Thread $thread): bool`** — Deletes thread from platform. Returns true if successful.

- **`Platform::fetchMessages(Thread $thread, int $limit, string|int|null $after): MessageList`** — Fetches messages from platform with pagination.

- **`Platform::reply(Assistant $assistant, Thread $thread, string $message): Message`** — Sends message to thread and returns response.

- **`Repository::fetchAssistant(Blueprint $blueprint, string $serviceName): ?Assistant`** — Fetches assistant from repository by blueprint and service name.

- **`Repository::createAssistant(Blueprint $blueprint, string $serviceName): Assistant`** — Creates new assistant entity.

- **`Repository::storeAssistant(Assistant $assistant): void`** — Persists assistant to repository.

- **`Repository::deleteAssistant(Assistant $assistant): bool`** — Deletes assistant from repository.

- **`Repository::fetchThread(Blueprint $blueprint, Subject $subject): ?Thread`** — Fetches thread from repository by blueprint and subject.

- **`Repository::createThread(Blueprint $blueprint, Subject $subject): Thread`** — Creates new thread entity.

- **`Repository::storeThread(Thread $thread): void`** — Persists thread to repository.

- **`Repository::deleteThread(Thread $thread): bool`** — Deletes thread from repository.

- **`Message::addContent(Content $content): void`** — Adds content item to message.

- **`Message::getAllContent(): array`** — Returns all content items.

- **`Message::getTextContent(): ?string`** — Extracts and concatenates all text content.

- **`Message::getJsonContent(): array`** — Extracts JSON content. Throws UnexpectedValue exception if no JSON content found.

## Dependencies

### Decode Labs

- **`archetype`** — Used for class resolution of Blueprints, Generators, and Platforms.

- **`atlas`** — Used for file operations (attaching files to assistants).

- **`coercion`** — Used for type coercion throughout the package.

- **`dictum`** — Used for text transformations (slug, name, ID formatting).

- **`exceptional`** — Used for exception handling throughout the package.

- **`kingdom`** — Used for Service interface implementation.

- **`monarch`** — Used for service access and path resolution.

- **`slingshot`** — Used for dependency injection and named instance resolution.

### External

- **`nesbot/carbon`** — Used for date/time handling throughout the package.

## Behaviour & Contracts

### Invariants

- Assistants are uniquely identified by blueprint action and service name
- Threads are uniquely identified by blueprint action and subject
- Assistants require service ID before threads can be created
- Threads require service ID before messages can be sent
- Thread status determines readiness (Queued/InProgress/RequiresAction = not ready)
- Blueprint actions are normalized to slug format
- Blueprint names are derived from actions via Dictum
- Generators can be Blueprints
- Language model selection is delegated to platforms
- Repository is resolved lazily via Slingshot
- Platforms are resolved per request via Slingshot
- Messages contain ordered content items
- Content items have associated mediums
- Polling attempts are limited to prevent infinite loops

### Input & Output Contracts

- **`Prophet::loadBlueprint(string $name): Blueprint`** — Returns Blueprint instance. Name is normalized to ID format via Dictum. Resolves via Slingshot named instance resolution.

- **`Prophet::loadGenerator(string $name): Generator`** — Returns Generator instance. Tries Generator resolution first. Falls back to Blueprint resolution if Blueprint is also Generator. Throws NotFound exception if cannot resolve.

- **`Prophet::generate(string $name, Subject $subject): mixed`** — Returns generated content. Loads generator and calls generate() with subject.

- **`Prophet::loadAssistant(string|Blueprint $blueprint, string $serviceName): Assistant`** — Returns Assistant instance. Fetches from repository or creates if not found. Finds existing assistant in platform by name. Creates assistant in platform if service ID is null. Suggests and sets language model if not set. Stores assistant if modified.

- **`Prophet::loadFreshAssistant(string|Blueprint $blueprint, string $serviceName): Assistant`** — Returns Assistant instance. Loads assistant and checks for model updates. Updates assistant if new model should be used. Stores assistant if modified.

- **`Prophet::updateAssistant(Assistant $assistant): void`** — Updates assistant in platform. Throws Runtime exception if update fails. Stores assistant in repository.

- **`Prophet::deleteAssistant(Assistant $assistant): bool`** — Deletes assistant from platform if service ID exists. Deletes from repository. Returns true if deleted.

- **`Prophet::loadThread(string|Blueprint $blueprint, Subject $subject): Thread`** — Returns Thread instance. Fetches from repository or creates if not found. Loads fresh assistant. Starts thread in platform if service ID is null. Refreshes thread if not ready. Stores thread if modified.

- **`Prophet::loadAndPollThread(string|Blueprint $blueprint, Subject $subject): Thread`** — Returns ready Thread instance. Loads thread and polls until ready.

- **`Prophet::pollThread(Thread $thread, int $attempts = 5): Thread`** — Returns ready Thread instance. Polls up to attempts times, sleeping 3 seconds between attempts. Throws Runtime exception if not ready after max attempts.

- **`Prophet::refreshThread(Thread $thread): void`** — Refreshes thread from platform. Throws Runtime exception if thread not initialized (service ID is null). Stores thread in repository.

- **`Prophet::deleteThread(Thread $thread): bool`** — Deletes thread from platform if service ID exists. Deletes from repository. Returns true if deleted.

- **`Prophet::fetchMessages(Thread $thread, int $limit = 20, ?string $afterId = null): MessageList`** — Returns MessageList. Returns empty list if thread not initialized. Delegates to platform.

- **`Prophet::reply(Thread $thread, string $message): Message`** — Returns Message response. Throws Runtime exception if thread not initialized. Loads assistant, sends message via platform, stores thread.

- **`Blueprint::generateAdditionalInstructions(Subject $subject): ?string`** — Returns subject-specific instructions or null. Called when starting a thread.

- **`BlueprintTrait::getAction(): string`** — Returns slug of class short name via Dictum.

- **`BlueprintTrait::getName(): string`** — Returns name derived from action via Dictum.

- **`BlueprintTrait::getMedium(): Medium`** — Returns Medium::Text by default.

- **`BlueprintTrait::getLanguageModelLevel(): LanguageModelLevel`** — Returns LanguageModelLevel::Standard by default.

- **`Platform::suggestModel(Medium $medium, LanguageModelLevel $level, array $features): string`** — Returns model name string based on requirements.

- **`Platform::shouldUpdateModel(string $oldModel, string $newModel, Medium $medium, LanguageModelLevel $level, array $features): bool`** — Returns true if assistant should be updated to new model.

- **`Platform::findAssistant(Assistant $assistant): bool`** — Returns true if assistant found in platform. Updates assistant service ID if found.

- **`Platform::createAssistant(Assistant $assistant): void`** — Creates assistant in platform. Updates assistant with service ID.

- **`Platform::startThread(Assistant $assistant, Thread $thread, ?string $additionalInstructions): void`** — Creates thread in platform. Updates thread with service ID, run ID, and status.

- **`Platform::refreshThread(Thread $thread): void`** — Updates thread status from platform. Updates thread status, timestamps, and run ID.

- **`Platform::reply(Assistant $assistant, Thread $thread, string $message): Message`** — Sends message to platform. Returns assistant's response as Message.

- **`Message::getTextContent(): ?string`** — Returns concatenated text from all Text content items. Returns null if no text content.

- **`Message::getJsonContent(): array`** — Returns parsed JSON from first Json content item. Throws UnexpectedValue exception if no JSON content found.

- **`Thread::isReady(): bool`** — Returns true if completedAt is set and status is not Queued/InProgress/RequiresAction.

## Error Handling

Prophet uses the Exceptional pattern for error handling. Key exception types:

- **`Runtime`** — Thrown when thread is not initialized for operations requiring service ID (reply, refresh), when assistant update fails, when polling exceeds max attempts, or when bridge project is not inside a JavaScript package.

- **`NotFound`** — Thrown when generator cannot be resolved.

- **`UnexpectedValue`** — Thrown when message does not contain expected JSON content.

Exceptions preserve context and include detailed error messages. Platform implementations may throw additional exceptions.

## Configuration & Extensibility

### Extension Points

- **Custom Blueprints** — Implement `Blueprint` interface to define AI assistant configurations. Use `BlueprintTrait` for default implementations.

- **Custom Generators** — Implement `Generator` interface to create AI-powered content generators.

- **Custom Platforms** — Implement `Platform` interface to integrate with different AI service providers (OpenAI, Anthropic, etc.).

- **Custom Repositories** — Implement `Repository` interface to customize persistence layer (database, filesystem, cache).

- **Custom Content Types** — Implement `Content` interface for specialized content mediums.

- **Custom Subjects** — Implement `Subject` interface for domain-specific subject types.

- **Custom Assistants** — Extend `Assistant` interface for platform-specific assistant features. Use `AssistantTrait` for defaults.

- **Custom Threads** — Extend `Thread` interface for platform-specific thread features. Use `ThreadTrait` for defaults.

- **Custom Suggestions** — Extend `Suggestion` interface for platform-specific suggestion features. Use `SuggestionTrait` for defaults.

### Configuration

- **Blueprint Loading** — Blueprints are resolved via Slingshot named instance resolution, allowing flexible registration.

- **Platform Selection** — Platforms are loaded by name, allowing runtime selection of AI service.

- **Repository Implementation** — Repository is resolved via Slingshot, allowing custom persistence implementations.

- **Model Selection** — Language models are suggested by platforms based on medium, level, and features.

- **Polling Configuration** — Polling attempts default to 5 with 3-second sleep intervals.

- **Message Pagination** — Message fetching supports limit and after parameters for pagination.

## Interactions with Other Packages

- **Kingdom** — Prophet implements `Service` interface for container integration.

- **Slingshot** — Uses Slingshot for resolving Blueprints, Generators, Platforms, and Repository.

- **Archetype** — Used indirectly via Slingshot for class resolution.

- **Monarch** — Used for service access and path resolution.

- **Atlas** — Used for file operations when attaching files to assistants.

- **Dictum** — Used for text transformations (slug, name, ID formatting).

- **Prophet-OpenAI** — Platform implementation for OpenAI API.

## Usage Examples

### Basic Blueprint Definition

```php
use DecodeLabs\Prophet\Blueprint;
use DecodeLabs\Prophet\BlueprintTrait;
use DecodeLabs\Prophet\Service\Medium;
use DecodeLabs\Prophet\Service\LanguageModelLevel;
use DecodeLabs\Prophet\Service\Feature;

class CodeReviewer implements Blueprint
{
    use BlueprintTrait;
    
    public function getInstructions(): string
    {
        return 'You are a code reviewer. Analyze code and provide feedback.';
    }
    
    public function getMedium(): Medium
    {
        return Medium::Code;
    }
    
    public function getLanguageModelLevel(): LanguageModelLevel
    {
        return LanguageModelLevel::Advanced;
    }
    
    public function getFeatures(): array
    {
        return [Feature::Chat, Feature::TextFile];
    }
}
```

### Loading and Using Assistants

```php
use DecodeLabs\Prophet;

$prophet = Monarch::getService(Prophet::class);

// Load or create assistant
$assistant = $prophet->loadAssistant(
    blueprint: 'code-reviewer',
    serviceName: 'openai'
);

// Update assistant
$assistant->setInstructions('Updated instructions');
$prophet->updateAssistant($assistant);

// Delete assistant
$prophet->deleteAssistant($assistant);
```

### Managing Threads

```php
use DecodeLabs\Prophet\Subject\Generic;

$subject = new Generic('code', 'file-123');

// Load or create thread
$thread = $prophet->loadThread(
    blueprint: 'code-reviewer',
    subject: $subject
);

// Load and wait for ready
$thread = $prophet->loadAndPollThread('code-reviewer', $subject);

// Refresh thread
$prophet->refreshThread($thread);

// Delete thread
$prophet->deleteThread($thread);
```

### Sending and Receiving Messages

```php
// Send message and get response
$response = $prophet->reply($thread, 'Please review this code.');

// Get text content
$text = $response->getTextContent();

// Get JSON content
$json = $response->getJsonContent();

// Fetch message history
$messages = $prophet->fetchMessages($thread, limit: 20);

foreach ($messages->getAllMessages() as $message) {
    echo $message->getRole()->value . ': ' . $message->getTextContent();
}

// Pagination
if ($messages->hasMore()) {
    $moreMessages = $prophet->fetchMessages(
        $thread,
        limit: 20,
        afterId: $messages->getLast()
    );
}
```

### Using Generators

```php
use DecodeLabs\Prophet\Generator;
use DecodeLabs\Prophet\BlueprintTrait;

class SummaryGenerator implements Generator, Blueprint
{
    use BlueprintTrait;
    
    public function getInstructions(): string
    {
        return 'Generate a concise summary.';
    }
    
    public function generate(Subject $subject): string
    {
        $prophet = Monarch::getService(Prophet::class);
        $thread = $prophet->loadAndPollThread($this, $subject);
        $response = $prophet->reply($thread, 'Generate summary.');
        return $response->getTextContent() ?? '';
    }
}

// Use generator
$summary = $prophet->generate('summary-generator', $subject);
```

### Custom Subjects

```php
use DecodeLabs\Prophet\Subject;

class DocumentSubject implements Subject
{
    public function __construct(
        private string $documentId
    ) {}
    
    public function getSubjectType(): string
    {
        return 'document';
    }
    
    public function getSubjectId(): ?string
    {
        return $this->documentId;
    }
}
```

### Thread Serialization

```php
// Serialize thread with messages
$data = $prophet->serializeThreadWithMessages(
    thread: $thread,
    limit: 50,
    afterId: 'msg-123'
);

// Returns array with thread data and messages
```

## Implementation Notes (for Contributors)

### Architecture

- **Service-Oriented Design** — Prophet acts as a service facade coordinating between Blueprints, Platforms, and Repository.

- **Blueprint Pattern** — Blueprints define AI assistant configuration separately from platform-specific implementation.

- **Platform Abstraction** — Platform interface abstracts AI service provider details, enabling multi-platform support.

- **Repository Pattern** — Repository interface abstracts persistence, allowing different storage backends.

- **Generator Pattern** — Generators extend Blueprints with typed output and generation logic.

- **Subject Pattern** — Subjects represent the context or target of AI assistance, enabling typed thread management.

- **Lazy Loading** — Repository is resolved lazily via Slingshot on first access.

- **Named Resolution** — Blueprints, Generators, and Platforms are resolved by name via Slingshot.

- **Model Suggestion** — Platforms suggest appropriate models based on requirements, with update logic for fresh assistants.

- **Polling Pattern** — Thread polling uses sleep-based polling with configurable attempts.

- **Message Content** — Messages contain ordered content items supporting multiple mediums.

### Performance Considerations

- Lazy repository initialization defers work until needed
- Service instances are cached by Slingshot
- Platform instances are resolved per request (stateless)
- Polling uses 3-second sleep intervals to avoid API rate limits
- Message pagination reduces memory usage for long conversations

### Design Decisions

- **Blueprint-Based Configuration** — Separating assistant configuration from implementation enables reuse across platforms.

- **Platform Abstraction** — Abstracting platform details enables testing and multi-provider support.

- **Repository Abstraction** — Abstracting persistence enables flexible storage backends.

- **Generator Pattern** — Combining Blueprint and Generator enables typed, reusable AI operations.

- **Subject Pattern** — Using subjects for thread context enables typed, domain-specific thread management.

- **Polling with Sleep** — Using sleep-based polling balances responsiveness with API rate limits.

- **Service Integration** — Implementing Kingdom Service enables container-based access.

- **Slingshot Resolution** — Using Slingshot for named resolution provides flexible dependency injection.

- **Carbon Integration** — Using Carbon for date/time handling provides rich time manipulation.

- **JSON Serialization** — All entities implement JsonSerializable for API response generation.

## Testing & Quality

**Code Quality:** 2.5/5 — Developing, early-stage codebase with evolving functionality.

**README Quality:** 2.5/5 — Minimal documentation noting "Coming soon..." for usage examples.

**Documentation:** 0/5 — No formal documentation beyond README.

**Tests:** 0/5 — No test suite currently.

See `composer.json` for supported PHP versions.

## Roadmap & Future Ideas

- Enhanced documentation and API reference
- Test suite implementation
- Usage examples in README
- Additional platform implementations (Anthropic, Gemini)
- Streaming response support
- Function calling and tool integration
- Vector store and retrieval augmented generation (RAG)
- Multi-modal assistant support
- Cost tracking and quota management
- Rate limiting and retry logic
- Conversation branching and forking
- Message editing and deletion
- Structured output validation
- Template-based prompt generation

## References

- [Decode Labs Chorus](https://github.com/decodelabs/chorus)
- [Prophet Repository](https://github.com/decodelabs/prophet)
- [Prophet-OpenAI Repository](https://github.com/decodelabs/prophet-openai)

